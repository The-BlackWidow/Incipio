<?php

namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PersonneRepository extends EntityRepository
{
    public function getMembreOnly()
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('n')->from('mgatePersonneBundle:Personne', 'n')
          ->where('n.membre IS NOT NULL')
          ->orderBy('n.prenom','ASC')
          ->addOrderBy('n.nom','ASC');
        return $query;
    }
    
    /**
     * Renvoi tous les membres qui ont été au poste de $poste pendant un mandat.
     * Il est possible d'utiliser les metacaractères spécifiques à mySQL tel que % pour vos recherches.
     * 
     * @access  public
     * @return Array(mgate\PersonneBundle\Entity\Membre)
     */
    public function getMembresByPoste($poste)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb
                ->select('p')
                ->from('mgatePersonneBundle:Personne', 'p')
                ->innerJoin('p.membre', 'me')
                ->innerJoin('me.mandats', 'ma')
                ->innerJoin('ma.poste', 'po')
                ->where("po.intitule LIKE '$poste'")
                ->orderBy('ma.finMandat','DESC');
        return $query;
    }
    
    public function getLastMembresByPoste($poste)
    {
        return $this->getMembresByPoste($poste)->setMaxResults(1);
    }
    
    
    public function getEmployeOnly($prospect = null)
    {
        
        $qb = $this->_em->createQueryBuilder();
        
        if($prospect != null)
        {
            $query = $qb->select('p')
                        ->from('mgatePersonneBundle:Personne', 'p')
                        ->leftJoin('p.employe', 'e')
                        ->addSelect('e')
                        ->where('p.employe IS NOT NULL')
                        ->andWhere('e.prospect = :prospect')
                            ->setParameter('prospect', $prospect);
        }
        else
        {
            $query = $qb->select('n')->from('mgatePersonneBundle:Personne', 'n')
                        ->where('n.employe IS NOT NULL');
            
        }
          
                    
                    
        return $query;
    }
    
    public function getMembreNotUser($user = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('n')->from('mgatePersonneBundle:Personne', 'n')
          ->where('n.user IS NULL')
          ->andWhere('n.membre IS NOT NULL');
        
        if($user)
        {
          $query->orWhere('n.user = :user')
            ->setParameter('user', $user);
        }
        
        
        
        return $query;
    }
}
