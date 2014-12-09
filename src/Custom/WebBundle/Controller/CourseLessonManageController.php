<?php 

namespace Custom\WebBundle\Controller;

use Topxia\WebBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

class CourseLessonManageController extends BaseController
{
    public function createAction(Request $request,$id)
    {
        $course = $this->getCourseService()->tryManageCourse($id);

        $categoryId = $course['subjectIds'];

        $category = $this->getCategoryService()->getCategory($categoryId[0]);

        if ($request->getMethod() == "POST") {
            $formData = $request->request->all();
            $lesson = $this->getCourseService()->createLesson($formData);
            return $this->render('TopxiaWebBundle:CourseLessonManage:list-item.html.twig', array(
                'course' => $course,
                'lesson' => $lesson,
            ));
        }

        return $this->render('CustomWebBundle:CourseLessonManage:lesson-modal.html.twig', array(
            'course' => $course,
            'category' => $category
        ));
    }

    public function editAction(Request $Request,$courseId,$lessonId)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $lesson = $this->getCourseService()->getCourseLesson($course['id'], $lessonId);
        if (empty($lesson)) {
            throw $this->createNotFoundException("课时(#{$lessonId})不存在！");
        }

        if($request->getMethod() == 'POST'){

            return $this->render('TopxiaWebBundle:CourseLessonManage:list-item.html.twig', array(
                'course' => $course,
                'lesson' => $lesson
            ));
        }

        return $this->render('CustomWebBundle:CourseLessonManage:lesson-modal.html.twig', array(
            'course' => $course,
            'category' => $category,
            'lesson' => $lesson
        ));
    }

    private function getCategoryService()
    {
        return $this->getServiceKernel()->createService('Taxonomy.CategoryService');
    }

    private function getCourseService()
    {
        return $this->getServiceKernel()->createService('Course.CourseService');
    }
}