<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        // replace this example code with whatever you need
        return $this->render('default/home.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/search/{page}", name="search")
     */
    public function searchAction($page)
    {

        $session = new Session();                   // start a session

        $request = Request::createFromGlobals();
        $search = $request->request->get('search');


        if (empty($request->request->get('search'))){
            $search = $session->get('search');
        }

        $session->set('search', $search);             // store the search keyword in a session

        return $this->resultAction($page, $search);

    }

    public function resultAction($page, $search){


        if (!empty($search)){
            $api_key = 'b2699c56582f54b7ea6c1d312716073a';

            $search = str_replace(" ","%20",$search);  // change the spaces in the url
            $url = "https://api.themoviedb.org/3/search/movie?api_key=".$api_key."&query=".$search."";  //API URL including api_key

            $results = file_get_contents($url);
            $results = json_decode($results, true); // decode the json results
            $results = $results['results'];

            $count = count($results);

            if ( ($page * 3) >= $count ){
                die("No search found");
            }
            $results = array_slice($results, $page*3, 3);
//echo "<pre>"; var_dump($results); die;
            return $this->render('default/result.html.twig', array(
                'results' => $results,
                'count' =>  $count,
                'page' => $page,
            ));
        }
        else{
            die("No search found");
        }

    }

}
