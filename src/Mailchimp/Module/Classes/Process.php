<?php

namespace RefinedDigital\ProductManager\Module\Classes;

use RefinedDigital\FormBuilder\Module\Contracts\FormBuilderCallbackInterface;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormBuilderRepository;


class Process implements FormBuilderCallbackInterface {

    public function run($request, $form) {
        $formBuilderRepository = new FormBuilderRepository();
        $formBuilderRepository->compileAndSend($request, $form);
    }
}
