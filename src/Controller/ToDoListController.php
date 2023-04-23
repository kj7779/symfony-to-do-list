<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TaskType;

class ToDoListController extends AbstractController
{
    #[Route('/', name: 'app_to_do_list')]
    public function index(EntityManagerInterface $entityManager, TaskRepository $taskRepository,Request $request ): Response
    {
        $task = $entityManager->getRepository(Task::class);
        $tasks=$task->findAll();
        return $this->render('index.html.twig', ['tasks'=>$tasks]);
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(EntityManagerInterface $entityManager, Request $request,TaskRepository $taskRepository)
    {
        // input data
        $title = trim($request->request->get('title'));

        // check
        if(empty($title))
            return $this->redirectToRoute('app_to_do_list');

        // insert
        $task = new Task();
        $task->setTitle($title);
        $entityManager->persist($task);
        $entityManager->flush();

        // return
        return $this->redirectToRoute('app_to_do_list');
    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus($id,EntityManagerInterface $entityManager, Request $request,TaskRepository $taskRepository)
    {
        // read
        $task = $entityManager->getRepository(Task::class)->find($id);

        // change status
        $task->setStatus(!$task->isStatus());
        $entityManager->flush();

        // return
        return $this->redirectToRoute('app_to_do_list');
    }

    /**
     * @Route("/delete/{id}", name="delete_task")
     */
    public function delete(Task $id, EntityManagerInterface $entityManager, Request $request,TaskRepository $taskRepository)
    {
        $entityManager->remove($id);
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do_list');
    }
}
