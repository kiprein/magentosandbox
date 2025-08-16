<?php
class CrysD_Carousel_Block_Adminhtml_Carousel_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_carousel');

        $form = new Varien_Data_Form([
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ]);
        $form->setUseContainer(true);

        // Carousel settings
        $fieldset = $form->addFieldset('carousel_fieldset', ['legend' => 'Carousel Settings']);

        $fieldset->addField('identifier', 'text', [
            'label'    => 'Identifier',
            'name'     => 'identifier',
            'required' => true,
        ]);

        $fieldset->addField('headline', 'text', [
            'label' => 'Headline',
            'name'  => 'headline',
        ]);

        $fieldset->addField('subheadline', 'text', [
            'label' => 'Subheadline',
            'name'  => 'subheadline',
        ]);

        $fieldset->addField('body', 'editor', [
            'label' => 'Body Copy',
            'name'  => 'body',
            'config'=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
        ]);

        $fieldset->addField('style', 'select', [
            'label'  => 'Style',
            'name'   => 'style',
            'values' => Mage::getSingleton('carousel/source_style')->toOptionArray(),
        ]);

        $fieldset->addField('theme', 'select', [
            'label'  => 'Theme',
            'name'   => 'theme',
            'values' => Mage::getSingleton('carousel/source_theme')->toOptionArray(),
        ]);

        // Slides dynamicRows
        $fieldset = $form->addFieldset('slides_fieldset', ['legend' => 'Slides']);

        $slidesValues = [];
        if ($model->getId()) {
            foreach ($model->getSlides() as $slide) {
                $slidesValues[] = $slide->getData();
            }
        }

        $fieldset->addField('slides', 'dynamicRows', [
            'label'   => 'Slides',
            'name'    => 'slides',
            'style'   => 'width:800px;',
            'values'  => $slidesValues,
            'columns' => [
                'slide_headline'    => ['label'=>'Headline',    'style'=>'width:150px;'],
                'slide_subheadline' => ['label'=>'Subheadline', 'style'=>'width:150px;'],
                'slide_body'        => [
                    'label'=>'Body',
                    'style'=>'width:250px;',
                    'config'=> ['wysiwyg'=>true, 'plugins'=>[], 'height'=>'150px']
                ],
                'slide_link'        => ['label'=>'Link URL',    'style'=>'width:150px;'],
                'slide_theme'       => [
                    'label'=>'Theme',
                    'type' =>'select',
                    'values'=> Mage::getSingleton('carousel/source_theme')->toOptionArray(),
                    'style'=>'width:100px;',
                ],
            ],
            'add_button_label' => 'Add Slide',
        ]);

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
