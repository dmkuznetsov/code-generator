<?php
declare(strict_types=1);

namespace App\UI\Controller\_CG_MODULE_;

use App\Application\_CG_MODULE_\_CG_MODULE_Service;
use App\Application\ResponseFactory;
use App\Application\RequestFormValidationHelper;
use App\UI\Controller\_CG_MODULE_\Model\_CG_FILE_NAME_RequestModel;
use App\UI\Controller\_CG_MODULE_\Form\_CG_FILE_NAME_Form;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class _CG_FILE_NAME_Controller extends AbstractController
{
    /**
     * @var _CG_MODULE_Service
     */
    private $_MODULEVAR_Service;

    public function __construct(_CG_MODULE_Service $_MODULEVAR_Service)
    {
        $this->_MODULEVAR_Service = $_MODULEVAR_Service;
    }

    /**
     * _DESCRIPTION_
     *
     * @SWG\Tag(name="_TAG_", description="_DESCRIPTION_"),
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(ref=@Model(type=\App\UI\Controller\_CG_MODULE_\Model\_FILENAME_RequestModel::class)),
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="object",
     *         allOf={
     *             @SWG\Schema(ref="#/definitions/JsonResponseOk"),
     *             @SWG\Schema(
     *                 @SWG\Property(
     *                     property="data",
     *                     ref=@Model(type=\App\Application\_CG_MODULE_\Dto\_FILENAME_Dto::class)
     *                 )
     *             )
     *         }
     *     )
     * ),
     * @SWG\Response(response=400, description="Bad Request", @SWG\Schema(ref="#/definitions/JsonResponseError")),
     * @SWG\Response(response=500, description="Internal Server Error", @SWG\Schema(ref="#/definitions/JsonResponseException")),
     *
     * @Route("_URL_", methods={"_HTTPMETHOD_"})
     *
     * @param Request $request
     * @throws \App\Application\Exception\ValidationException
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $username = $this->getUser()->getUsername();
        $model = new _FILENAME_RequestModel();
        $form = $this->createForm(_FILENAME_Form::class, $model)
            ->submit($request->request->all());
        RequestFormValidationHelper::validate($form);
        $result = $this->_MODULEVAR_Service->_ACTION__CG_MODULE_($username);

        return ResponseFactory::createOkResponse($request, $result);
    }
}
