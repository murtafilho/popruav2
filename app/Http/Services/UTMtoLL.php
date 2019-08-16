<?php
namespace App\Http\Services;
use App\Http\Services\Gpoint;
class UTMtoLL{

        public function convert($leste,$norte){
            $gp = new Gpoint;
            $gp->setUTM($leste,$norte);
            $gp->convertTMtoLL();
            return [$gp->Lat(),$gp->Long()];
        }

}