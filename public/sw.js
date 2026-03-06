/**
 * POPRUA v2 - Service Worker
 * Gerencia upload offline-first de imagens de vistorias.
 * Armazena fotos no IndexedDB quando offline e sincroniza via Background Sync.
 */

const DB_NAME = 'poprua-offline';
const DB_VERSION = 1;
const STORE_NAME = 'pending-uploads';

// ===== IndexedDB helpers =====

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                const store = db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
                store.createIndex('status', 'status', { unique: false });
                store.createIndex('vistoriaId', 'vistoriaId', { unique: false });
            }
        };
        request.onsuccess = (e) => resolve(e.target.result);
        request.onerror = (e) => reject(e.target.error);
    });
}

function getAllPending() {
    return openDB().then(db => {
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readonly');
            const store = tx.objectStore(STORE_NAME);
            const index = store.index('status');
            const request = index.getAll('pending');
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    });
}

function updateStatus(id, status) {
    return openDB().then(db => {
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            const getReq = store.get(id);
            getReq.onsuccess = () => {
                const record = getReq.result;
                if (record) {
                    record.status = status;
                    store.put(record);
                }
                resolve();
            };
            getReq.onerror = () => reject(getReq.error);
        });
    });
}

function deleteRecord(id) {
    return openDB().then(db => {
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            const request = store.delete(id);
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    });
}

// ===== Upload logic =====

async function uploadPendingImages() {
    const pending = await getAllPending();

    if (pending.length === 0) {
        return;
    }

    for (const record of pending) {
        try {
            await updateStatus(record.id, 'uploading');

            const formData = new FormData();
            formData.append('foto', record.blob, record.filename);
            formData.append('vistoria_id', record.vistoriaId);
            if (record.descricao) {
                formData.append('descricao', record.descricao);
            }

            const response = await fetch(record.uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': record.csrfToken,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                await deleteRecord(record.id);
                // Notify the client
                self.clients.matchAll().then(clients => {
                    clients.forEach(client => {
                        client.postMessage({
                            type: 'UPLOAD_SUCCESS',
                            recordId: record.id,
                            vistoriaId: record.vistoriaId,
                            filename: record.filename,
                        });
                    });
                });
            } else if (response.status >= 400 && response.status < 500) {
                // Client error - don't retry
                await updateStatus(record.id, 'error');
                self.clients.matchAll().then(clients => {
                    clients.forEach(client => {
                        client.postMessage({
                            type: 'UPLOAD_ERROR',
                            recordId: record.id,
                            filename: record.filename,
                            status: response.status,
                        });
                    });
                });
            } else {
                // Server error - revert to pending for retry
                await updateStatus(record.id, 'pending');
                throw new Error(`Server error: ${response.status}`);
            }
        } catch (error) {
            await updateStatus(record.id, 'pending');
            throw error; // Re-throw to trigger Background Sync retry
        }
    }
}

// ===== Service Worker Events =====

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Background Sync event (Chromium browsers)
self.addEventListener('sync', (event) => {
    if (event.tag === 'upload-fotos') {
        event.waitUntil(uploadPendingImages());
    }
});

// Listen for messages from the main thread
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'TRIGGER_UPLOAD') {
        event.waitUntil(uploadPendingImages());
    }
});
