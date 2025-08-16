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

        // existing IDs to detect deletions
        $existingIds = $adapter->fetchCol(
            $adapter->select()->from($table, 'slide_id')->where('carousel_id = ?', $carouselId)
        );

        $keep = array();

        foreach ($slidesData as $position => $slideData) {
            $position = (int)$position;

            // base fields
            $base = array(
                'carousel_id' => $carouselId,
                'position' => $position,
                'slide_headline' => isset($slideData['slide_headline']) ? $slideData['slide_headline'] : null,
                'slide_subheadline' => isset($slideData['slide_subheadline']) ? $slideData['slide_subheadline'] : null,
                'slide_body' => isset($slideData['slide_body']) ? trim($slideData['slide_body']) : null,
                'slide_link' => isset($slideData['slide_link']) ? $slideData['slide_link'] : null,
                'slide_link_cta' => isset($slideData['slide_link_cta']) ? $slideData['slide_link_cta'] : null,
                'slide_theme'       => isset($slideData['slide_theme']) ? $slideData['slide_theme'] : null,
            );

            if (!empty($slideData['slide_id'])) {
                // UPDATE: only set image columns if a new value (or delete flag) is provided
                $sid  = (int)$slideData['slide_id'];
                $data = $base;

                if (isset($slideData['slide_image']) && $slideData['slide_image'] !== '') {
                    $data['slide_image'] = $slideData['slide_image'];
                }
                if (!empty($slideData['delete_image'])) {
                    $data['slide_image'] = null;
                }

                if (isset($slideData['slide_image_mobile']) && $slideData['slide_image_mobile'] !== '') {
                    $data['slide_image_mobile'] = $slideData['slide_image_mobile'];
                }
                if (!empty($slideData['delete_image_mobile'])) {
                    $data['slide_image_mobile'] = null;
                }

                $adapter->update($table, $data, array('slide_id = ?' => $sid));
                $keep[] = $sid;

            } else {
                // INSERT: include whatever we have (NULL if not provided)
                $data = $base + array(
                    'slide_image'        => (isset($slideData['slide_image']) && $slideData['slide_image'] !== '') ? $slideData['slide_image'] : null,
                    'slide_image_mobile' => (isset($slideData['slide_image_mobile']) && $slideData['slide_image_mobile'] !== '') ? $slideData['slide_image_mobile'] : null,
                );

                $adapter->insert($table, $data);
                $keep[] = (int)$adapter->lastInsertId($table);
            }
        }

        // delete removed rows
        $toDelete = array_diff($existingIds, $keep);
        if (!empty($toDelete)) {
            $adapter->delete($table, array('slide_id IN (?)' => $toDelete));
        }
    }
}
