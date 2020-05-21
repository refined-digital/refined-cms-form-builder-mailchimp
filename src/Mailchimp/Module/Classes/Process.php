<?php

namespace RefinedDigital\FormBuilder\Mailchimp\Module\Classes;

use RefinedDigital\FormBuilder\Module\Contracts\FormBuilderCallbackInterface;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormBuilderRepository;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormsRepository;
use Newsletter;


class Process implements FormBuilderCallbackInterface {

    public function run($request, $form) {
        $formBuilderRepository = new FormBuilderRepository();
        $formBuilderRepository->compileAndSend($request, $form);
        $formRepo = new FormsRepository($formBuilderRepository);
        $fields = $formRepo->formatFieldsByName($request, $form);

        $firstName = '';
        $lastName = '';

        if ($fields['Name']) {
            $name = explode(' ', $fields['Name']);
            $firstName = $name[0];
            if (isset($name[1])) {
                $lastName = $name[1];
            }
        }

        if (isset($fields['First Name']) && $fields['First Name']) {
            $firstName = $fields['First Name'];
        }

        if (isset($fields['Last Name']) && $fields['Last Name']) {
            $lastName = $fields['Last Name'];
        }

        $mergeTags = [
            'FNAME' => $firstName,
            'LNAME' => $lastName
        ];

        Newsletter::subscribeOrUpdate($fields['Email'], $mergeTags);
    }
}
