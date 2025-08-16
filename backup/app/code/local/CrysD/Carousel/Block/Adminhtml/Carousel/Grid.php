<?php
class CrysD_Carousel_Block_Adminhtml_Carousel_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('carouselGrid');
        $this->setDefaultSort('carousel_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('carousel/carousel')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('carousel_id', [
            'header' => 'ID',
            'index'  => 'carousel_id',
            'width'  => '50px',
        ]);

        $this->addColumn('identifier', [
            'header' => 'Identifier',
            'index'  => 'identifier',
        ]);

        $this->addColumn('headline', [
            'header' => 'Headline',
            'index'  => 'headline',
        ]);

        $this->addColumn('style', [
            'header' => 'Style',
            'index'  => 'style',
        ]);

        $this->addColumn('theme', [
            'header' => 'Theme',
            'index'  => 'theme',
        ]);

        $this->addColumn('carousel_link', [
            'header' => 'Carousel Link',
            'index'  => 'carousel_link',
        ]);
        
        $this->addColumn('carousel_cta', [
            'header' => 'CTA',
            'index'  => 'carousel_cta',
        ]);
        
        $this->addColumn('video', [
            'header' => 'Video Link',
            'index'  => 'video',
        ]);

        $this->addColumn('visible_images', [
            'header' => 'Visible Images',
            'index'  => 'visible_images',
        ]);

        $this->addColumn('action', [
            'header'   => 'Action',
            'width'    => '100',
            'type'     => 'action',
            'getter'   => 'getId',
            'actions'  => [[
                'caption' => 'Edit',
                'url'     => ['base' => '*/*/edit'],
                'field'   => 'id'
            ]],
            'filter'   => false,
            'sortable' => false,
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
