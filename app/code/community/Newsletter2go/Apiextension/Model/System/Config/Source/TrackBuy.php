<?php

class Newsletter2go_Apiextension_Model_System_Config_Source_TrackBuy
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => 'No',
                'value' => 1,
            ),
            array(
                'label' => 'Yes',
                'value' => 0,
            ),
        );
    }
}

