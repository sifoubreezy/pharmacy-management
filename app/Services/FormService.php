<?php
namespace App\Services;

use App\Models\Form;
use App\Repositories\FormRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25
 */

class FormService
{
    private $formRepository;

    /**
     * PostService constructor.
     * @param FormRepository $formRepository
     */
    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function getForms(){
        return $this->formRepository->getFormsOrderedByFormAsc();
    }


    public function getFormsPaginated(int $perPage)
    {
        return $this->formRepository->getFormsOrderedByFormAscPaginated($perPage);
    }

}