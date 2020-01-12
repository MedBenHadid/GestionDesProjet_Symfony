<?php

namespace BalProjetBundle\Controller;

use BalProjetBundle\Entity\Equipe;
use BalProjetBundle\Entity\Projet;
use BalProjetBundle\Form\EquipeType;
use BalProjetBundle\Form\ProjetType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {


        $projet = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findAll();
        $form = $this->createFormBuilder()
            ->add('Chercher', TextType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->getData('Chercher')['Chercher']!=''){

            $projet = $this->getDoctrine()
                ->getRepository(Projet::class)
                ->findBy(array('libelle'=>$form->getData('Chercher')['Chercher']));

            if (empty($projet)){
                $invalid="invalid serch";
                return $this->render('@BalProjet/Default/index.html.twig',array('Form'=>$form->createView(), 'invalid'=>$invalid ,'projets'=>$projet));
            }

//            var_dump($form->getData('Chercher')['Chercher']);

        }else{
            $projet =$this->getDoctrine()->getRepository(Projet::class)->findAll();
        }
        return $this->render('@BalProjet/Default/index.html.twig',array('projets'=>$projet,'formRecherche'=>$form->createView()));

    }


    public function deleteAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $projetDeleted = $manager->getRepository(Projet::class)
            ->find($id);
        $manager->remove($projetDeleted);
        $manager->flush();

        return $this->redirectToRoute('List');
    }
    public function detailsAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $query= $em->createQuery('select t FROM BalProjetBundle\Entity\Equipe t WHERE t.Projet = :projet')->setParameter('projet', $id)->getResult();
        $projet= $em->createQuery('select t FROM BalProjetBundle\Entity\Projet t WHERE t.id = :projet')->setParameter('projet', $id)->getResult();
        $Top3= $em->createQuery('select t FROM BalProjetBundle\Entity\Equipe t ORDER BY  t.score DESC') ->setMaxResults(3)->getResult();
        return $this->render('@BalProjet/Default/ListParProjet.html.twig',array('Equipes'=>$query,'nameProjet'=>$projet,'Top3'=>$Top3));
    }


    public function updateAction(Request $request,$id){
//print_r($request);
        $manager = $this->getDoctrine()->getManager();
        $projet = $manager->getRepository(Projet::class)->find($id);
        //print_r($request);
        $form =  $this->createForm(ProjetType::class, $projet);

        $form =$form->handleRequest($request);
        if($form->isSubmitted()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($projet);
            $em->flush();
            return $this->redirectToRoute('List');
        }
        return $this->render('@BalProjet/Default/update.html.twig',array('form'=>$form->createView()));



        /*    $manager->remove($clubUpdate);
            $manager->flush();*/

        return $this->redirectToRoute('update/{$id}');

    }




    public function voteAction(Request $request,$id,$idEquipe,$vote)
    {
        $equipe = $this->getDoctrine()
            ->getRepository(Equipe::class)
            ->find($idEquipe);
        $equipe->setScore($equipe->getScore()+$vote);
        $em=$this->getDoctrine()->getManager();

        $em->persist($equipe);
        $em->flush();
            return $this->redirectToRoute('details', array(
            'id' => $id,
        ));

    }

    public function affecterAction(Request $request)
    {
//        $manager = $this->getDoctrine()->getManager();
//        $formationUpdate = $manager->getRepository(Equipe::class)->find($id);
        //print_r($request);
        $equipe= new Equipe();
        $form =  $this->createForm(EquipeType::class,$equipe);



//                  ou



//        $form = $this->createFormBuilder()
//            ->add('nom', TextType::class, ['required'=> false])
//
//            ->add('Projet',EntityType::class,['class'=>Projet::class,'query_builder'=>function(EntityRepository $em){
//                return $em->createQueryBuilder('u')->orderBy('u.libelle');
//            },'choice_label'=>'libelle'])
//
//            ->add('Class', ChoiceType::class, [
//                'choices'  => [
//                    '3A24' => "3A24",
//                    '3A25' => "3A25",
//                    '3A26' => "3a27",
//                ],
//            ])
//            ->add('send', SubmitType::class)
//            ->getForm();
//
//

        $form=$form->handleRequest($request);
        if($form->isSubmitted()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->redirectToRoute('List');
        }

        return $this->render('@BalProjet/Default/affecter.html.twig',array('f'=>$form->createView()));
    }



}
