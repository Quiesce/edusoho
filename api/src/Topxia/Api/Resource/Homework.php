<?php

namespace Topxia\Api\Resource;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Common\ArrayToolkit;

class Homework extends BaseResource
{
    public function get(Application $app, Request $request, $id)
    {
        $idType = $request->query->get('_idType');
        if ('lesson' == $idType) {
            $homework = $this->getHomeworkService()->getHomeworkByLessonId($id);
        } else {
            $homework = $this->getHomeworkService()->getHomework($id);
        }
        if (empty($homework)) {
            $homework = array();
            return $homework;
        }
        
        $items = $this->getHomeworkService()->findItemsByHomeworkId($homework['id']);
        $indexdItems = ArrayToolkit::index($items, 'questionId');
        $questions = $this->getQuestionService()->findQuestionsByIds(array_keys($indexdItems));
        $homework['items'] = $questions;
        return $this->filter($homework);
    }

    public function filter(&$res)
    {
        $res = ArrayToolkit::parts($res, array('id', 'courseId', 'lessonId', 'description', 'itemCount', 'items'));
        $items = $res['items'];
        $newItmes = array();
        foreach ($items as $item) {
            $item = ArrayToolkit::parts($item, array('id', 'type', 'stem', 'answer', 'analysis', 'metas', 'difficulty'));
            $newItmes[] = $item;
        }
        $res['items'] = $newItmes;
        return $res;
    }

    protected function getHomeworkService()
    {
        return $this->getServiceKernel()->createService('Homework:Homework.HomeworkService');
    }

    protected function getQuestionService()
    {
        return $this->getServiceKernel()->createService('Question.QuestionService');
    }
}
