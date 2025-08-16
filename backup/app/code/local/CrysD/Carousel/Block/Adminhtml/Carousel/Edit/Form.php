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
        
        $fieldset->addField('carousel_link', 'text', [
            'label' => 'Link',
            'name'  => 'carousel_link',
        ]);

        $fieldset->addField('carousel_cta', 'text', [
            'label' => 'CTA',
            'name'  => 'carousel_cta',
        ]);

        $fieldset->addField('video', 'text', [
            'label' => 'Video',
            'name'  => 'video',
        ]);

        $fieldset->addField('visible_images', 'text', [
            'label' => 'Visible Images',
            'name'  => 'visible_images',
        ]);

        // Slides dynamicRows
        $fieldset = $form->addFieldset('slides_fieldset', ['legend'=>'Slides']);

        $slides = $model->getId() ? $model->getSlides() : [];
        $html  = $this->getLayout()
            ->createBlock('carousel/adminhtml_carousel_edit_slides')
            ->setSlides($slides)
            ->toHtml();

        $fieldset->addField('slides_container', 'note', [
            'label' => 'Slides',
            'text'  => $html,
        ]);

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
