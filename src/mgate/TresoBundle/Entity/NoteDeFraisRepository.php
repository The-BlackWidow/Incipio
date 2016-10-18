<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NoteDeFraisRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NoteDeFraisRepository extends EntityRepository
{
    /**
     * 
     * @return array
     */
    public function findAllByMandat() {        
        $entities = $this->findBy(array(), array('mandat' => 'desc', 'date' => 'asc'));
        $nfsParMandat = array();
        foreach($entities as $nf){
            $mandat = $nf->getMandat();
            if(array_key_exists($mandat, $nfsParMandat))
                $nfsParMandat[$mandat][] = $nf;
            else
                $nfsParMandat[$mandat] = array($nf);
        }        
        return $nfsParMandat;
    }
    
    /**
     * Renvoie les NDF pour un mois donné
     * YEAR MONTH DAY sont défini dans DashBoardBundle/DQL (qui doit devenir FrontEndBundle)
     * @return array
     */
    public function findAllByMonth($month, $year, $trimestriel = false) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('f')
                     ->from('mgateTresoBundle:NoteDeFrais', 'f');
        if($trimestriel)
            $query->where("MONTH(f.date) >= $month")
                  ->andWhere("MONTH(f.date) < ($month + 2)");
        else
            $query->where("MONTH(f.date) = $month");
        
        $query->andWhere("YEAR(f.date) = $year")->orderBy("f.date");       
                    
        return $query->getQuery()->getResult();;
    }
}