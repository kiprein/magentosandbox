<?php
class CrysD_Carousel_Model_Mysql4_Slide extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('carousel/slide', 'slide_id');
    }

    public function saveSlides(CrysD_Carousel_Model_Carousel $carousel, array $slidesData)
    {
        $adapter    = $this->_getWriteAdapter();
        $table      = $this->getMainTable();
        $carouselId = (int)$carousel->getId();

        // fetch existing slide IDs
        $existing = $adapter->fetchCol(
            $adapter->select()
                ->from($table, 'slide_id')
                ->where('carousel_id = ?', $carouselId)
        );

        $keep = array();
        foreach ($slidesData as $position => $slideData) {
            // If no new image was uploaded but this is an existing slide,
            // load its current image so we don't wipe it out:
            if (empty($slideData['slide_image']) && !empty($slideData['slide_id'])) {
                $old = Mage::getModel('carousel/slide')
                           ->load($slideData['slide_id'])
                           ->getSlideImage();
                $slideData['slide_image'] = $old;
            }

            $data = array(
                'carousel_id' => $carouselId,
                'position' => (int)$position,
                'slide_headline' => isset($slideData['slide_headline']) ? $slideData['slide_headline'] : null,
                'slide_subheadline' => isset($slideData['slide_subheadline']) ? $slideData['slide_subheadline'] : null,
                'slide_body' => isset($slideData['slide_body']) ? $slideData['slide_body'] : null,
                'slide_link' => isset($slideData['slide_link']) ? $slideData['slide_link'] : null,
                'slide_link_cta' => isset($slideData['slide_link_cta']) ? $slideData['slide_link_cta'] : null,
                'slide_video' => isset($slideData['slide_video']) ? $slideData['slide_video'] : null,
                'slide_theme' => isset($slideData['slide_theme']) ? $slideData['slide_theme'] : null,
                'slide_image' => isset($slideData['slide_image']) ? $slideData['slide_image'] : null,
                'slide_image_mobile' => isset($slideData['slide_image_mobile']) ? $slideData['slide_image_mobile'] : null,
            );

            if (!empty($slideData['slide_id'])) {
                // update
                $adapter->update($table, $data, array('slide_id = ?' => (int)$slideData['slide_id']));
                $keep[] = (int)$slideData['slide_id'];
            } else {
                // insert
                $adapter->insert($table, $data);
                $keep[] = (int)$adapter->lastInsertId($table);
            }
        }

        // delete removed slides
        $toDelete = array_diff($existing, $keep);
        if (!empty($toDelete)) {
            $adapter->delete($table, array('slide_id IN (?)' => $toDelete));
        }
    }
}
