<?php

namespace App\View\Components;

use App\Models\Form;
use Illuminate\View\Component;

class AppForm extends Component
{
    public $identifier;
    public $identifierValue;
    public $form;
    public $formData;

    public function __construct($identifier,$identifierValue)
    {
        $this->identifier = $identifier;
        $this->identifierValue = $identifierValue;
        $this->form = Form::where($this->identifier,$this->identifierValue)->first();
        $this->formData = isset($this->form->form_data) ? $this->form->form_data : [];
    }

    public function render()
    {
        return view('components.app-form');
    }
}
