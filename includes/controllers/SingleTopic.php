<?php

class SingleTopic extends Controller
{
    public function GetTopic()
    {
        //TODO: Catch if the ID is wrong!!!!

        // Get the ID of the topic, sanatize it
        $iTopicId = $_GET['id'];
        // Check which page is it.
        if (!isset($_GET['page']) || $_GET['page'] == 0 ){
            /*
             * OBS: Since we should always start with the page 1, not 0
             * Server is redirecting to the correct page number. s
             */
            $url = $_SERVER['REQUEST_URI'];
            header("location:$url&page=1");
        } else {
            $iPageNumber = $_GET['page'] - 1 ;
            $iOffset = 5 * $iPageNumber;
        }

        /*
         * Using the model Topics (I think it should
         * be just Topic.
         * And then executing get_topic() function
         * that returns an object, which
         * is saved in $objTopic
         */
        $topic = new Topics();
        $objTopic = $topic->get_topic($iTopicId, $iOffset);
        if($objTopic == false){
            self::NotExistingPage();
        }

        // +1 since we're starting technically with 0 ;P
        $objTopic->currentPage = $iPageNumber+1;

        /*
         * Current URL is for the pagination,
         * we shouldn't have the static variable since
         * the url might be changed
         * And since user might pass different variables, all we need
         * is just the base url with the id, right?
         */
        $objTopic->currentUri = $_SERVER['REQUEST_URI'];

        // uncomment one below to see what we receive.
        // btw, I recomment some JSON Viewer extension
        //echo json_encode($objTopic);
        //die();


        /*
         * Create a view, pass object.
         * OBS: View is created here instead of routes,
         * otherwise it be impossible/hard to pass additional variables/data
         * Thanks, Peter, for the solution ;)
         *
         */
        self::CreateView('single_topic', 'single_topic', '', $objTopic);

    }


    public function create_topic(){
        // Validate all this input
        // NOTE: MATCH THE LENGTHS FROM THE DATABASE
        $_POST['topic_name'] = 'Test Topic';
        Validation::checkInput($_POST['topic_name'],'string',5,20);
        
        $_POST['category_id'] = 3;
        Validation::checkInput($_POST['category_id'],'integer',1,2);
        
        $_POST['user_id'] = 3;
        Validation::checkInput($_POST['user_id'],'integer','','');
        
        $_POST['content'] = 'Test Topic Test Topic Test Topic Test Topic Test Topic';
        Validation::checkInput($_POST['content'],'string',10,500);
        
        $aTopicData = $_POST;
        // echo $aTopicData['topic_name']; works
        echo "all good";

    }

}