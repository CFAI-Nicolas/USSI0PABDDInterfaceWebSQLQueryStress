<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


final class MainController extends AbstractController
{
    #[Route('/', name: 'app_login', methods: ['GET', 'POST'])]
public function login(Request $request, SessionInterface $session): Response
{
    if ($request->isMethod('POST')) {
        $server = $request->request->get('server');
        $database = $request->request->get('database');
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // Stocker les infos en session
        $session->set('sql_config', compact('server', 'database', 'username', 'password'));

        return $this->redirectToRoute('app_query');
    }

    return $this->render('main/index.html.twig');
}

#[Route('/connect', name: 'connect_sql', methods: ['POST'])]
public function connect(Request $request, SessionInterface $session): Response
{
    $host = $request->request->get('host');
    $port = $request->request->get('port');
    $db = $request->request->get('database');
    $user = $request->request->get('username');
    $pass = $request->request->get('password');

    $dsn = "sqlsrv:Server=$host,$port;Database=$db";

    try {
        $pdo = new \PDO($dsn, $user, $pass);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $session->set('sql_dsn', $dsn);
        $session->set('sql_user', $user);
        $session->set('sql_pass', $pass);
        return $this->redirectToRoute('query_page');
    } catch (\PDOException $e) {
        return new Response('Échec de connexion : ' . $e->getMessage());
    }
}


}
