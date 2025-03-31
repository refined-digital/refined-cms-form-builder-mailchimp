<?php

namespace RefinedDigital\Mailchimp\Module\Classes;

use RefinedDigital\FormBuilder\Module\Contracts\FormBuilderCallbackInterface;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormBuilderRepository;
use RefinedDigital\FormBuilder\Module\Http\Repositories\FormsRepository;
use Newsletter;


class Process implements FormBuilderCallbackInterface {

    public function run($request, $form) {
        $formBuilderRepository = new FormBuilderRepository();
        $formBuilderRepository->compileAndSend($request, $form);
        $formRepo = new FormsRepository($formBuilderRepository);
        $fields = [];

        // set the form fields
        if(isset($form->fields) && $form->fields->count()) {
            foreach($form->fields as $field) {
                $key = $field->merge_field;
                if(!$key) {
                    continue;
                }

                $value = $request[$field->field_name];
                if($key === 'Description') {
                    $value = $field->name.': '.$value;
                    if(isset($fields[$key])) {
                        $value = $fields[$key].PHP_EOL.$value;
                    }
                }
                $fields[$key] = $value;
            }
        }

        $options = config('mailchimp.options') ?? [];

        Newsletter::subscribeOrUpdate($fields['EMAIL'], $fields, '', $options);
    }
}
