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
        $error = null;
    
        if ($request->isMethod('POST')) {
            $server = $request->request->get('host');
            $port = $request->request->get('port');
            $database = $request->request->get('database');
            $username = $request->request->get('username');
            $password = $request->request->get('password');
    
            $dsn = "sqlsrv:Server=$server,$port;Database=$database";
    
            try {
                $pdo = new \PDO($dsn, $username, $password);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
                $session->set('sql_config', compact('server', 'port', 'database', 'username', 'password'));
    
                // ✔️ Redirection vers l'interface de requêtes
                return $this->redirectToRoute('app_query');
    
            } catch (\PDOException $e) {
                // ❌ Affiche l'erreur sur la vue
                $error = 'Échec de connexion : ' . $e->getMessage();
            }
        }
    
        return $this->render('main/index.html.twig', [
            'error' => $error
        ]);
    }
    

    #[Route('/query', name: 'app_query')]
    public function query(Request $request, SessionInterface $session): Response
    {
        $sqlConfig = $session->get('sql_config');
        if (!$sqlConfig) {
            return $this->redirectToRoute('app_login');
        }

        $results = [];
        $durationTotal = 0;
        $durationAvg = 0;

        if ($request->isMethod('POST')) {
            $query = $request->request->get('query');
            $count = (int) $request->request->get('count', 1);
            $delay = (int) $request->request->get('delay', 0);

            $dsn = "sqlsrv:Server={$sqlConfig['server']},{$sqlConfig['port']};Database={$sqlConfig['database']}";

            try {
                $pdo = new \PDO($dsn, $sqlConfig['username'], $sqlConfig['password']);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $startAll = microtime(true);
                for ($i = 0; $i < $count; $i++) {
                    $stmt = $pdo->query($query);
                    if ($stmt) {
                        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    }
                    usleep($delay * 1000); // délai en millisecondes
                }
                $durationTotal = microtime(true) - $startAll;
                $durationAvg = $durationTotal / $count;
            } catch (\PDOException $e) {
                $this->addFlash('error', 'Erreur SQL : ' . $e->getMessage());
            }
        }

        return $this->render('main/query.html.twig', [
            'results' => $results,
            'total' => $durationTotal,
            'avg' => $durationAvg,
        ]);
    }
}
