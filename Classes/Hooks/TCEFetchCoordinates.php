<?php
namespace Mia3\Mia3Location\Hooks;

class TCEFetchCoordinates
{
    function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$reference)
    {
        if ($table == 'tx_mia3location_domain_model_location') {
            $where = 'uid = ' . $id;
            $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                '*',
                $table,
                $where
            );
            if(!is_array($rows)) return;
            $row = current($rows);
            $address = implode(',', array(
                $row['street'],
                $row['zip'],
                $row['city'],
                'Deutschland',
            ));
            // if there is a longitude and latitude present
            // do not update these fields and just return
            if(
               (isset($row['longitude']) && !empty($row['longitude']) )
               && (isset($row['latitude']) && !empty($row['latitude']))) {
               return;
           }

            $apiURL = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false&language=de';
            $addressData = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($apiURL);
            $adr = json_decode($addressData);
            $coordinates = $adr->results[0]->geometry->location;
            if ($coordinates !== null) {
                if (!empty($fieldArray['latitude']) && $fieldArray['latitude'] !== $row['latitude']) {

                } else {
                    $fieldArray['latitude'] = $coordinates->lat;
                }
                if (!empty($fieldArray['longitude']) && $fieldArray['longitude'] !== $row['longitude']) {

                } else {
                    $fieldArray['longitude'] = $coordinates->lng;
                }
            }
        }
    }
}

?>
