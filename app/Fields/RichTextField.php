<?php

namespace Gigtrooper\Fields;


class RichTextField extends ElementField
{

    public function getName()
    {
        return "RichText";
    }

    public function getInputHtml($handle, $params = [])
    {
        $value = $this->getValue();

        if ($this->element->getModel() != null) {
            $model = $this->element->getModel();
            $modelId = $model->id;
        } else {
            $modelId = '0';
            $model = $this->element->initModel();
        }

        $label = $model->getLabel();

        $label = strtolower($label);

        $dir = "/$label/$modelId/";

        return view('fields.richtext', [
            'handle' => $handle,
            'dir' => $dir,
            'value' => $value,
            'title' => $this->getTitle(),
            'params' => $params,
            'basic' => (!empty($this->settings['options']['basic'])) ? $this->settings['options']['basic'] : false,
            'height' => (!empty($this->settings['options']['height'])) ? $this->settings['options']['height'] : 500,
            'maxChar' => (!empty($this->settings['options']['maxChar'])) ? $this->settings['options']['maxChar'] : null,
        ]);
    }
}