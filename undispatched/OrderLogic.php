<?php

namespace FespMVC\Model;

use FespMVC\Controller\ToolBox;
use FespMVC\Modules\Couriers\AmazonSFP;
use FespMVC\Model\Courier;

class OrderLogic
{
    public $accessOrders;
    public $accessDatabase;
    public $amazonSFP;
    public $handles;
    public $toolBox;

    public $couriers = [];

    public function __construct(AccessOrders $accessOrders = null)
    {
        if (is_null($accessOrders)) {
            $this->accessOrders = new AccessOrders();
            $this->accessDatabase = new AccessDatabase();
        } else {
            $this->accessOrders = $accessOrders;
            $this->accessDatabase = $this->accessOrders->accessDatabase;
        }
        $this->handles = $this->accessDatabase->handles;
        $this->toolBox = new ToolBox();
        $this->amazonSFP = new AmazonSFP($this->accessOrders);

        $this->couriers = [
          // Remember to change serviceCost values if you add/delete any couriers
          'WL' => new Courier([
             'code' => 'WL',
             'friendlyName' => 'Whistl Mail Large Letter',
             'maxWeight' => 750,
             'maxLength' => 25,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => 10,
             'serviceCost' => 1,
             'availableServices' => ['standard'],
             'integrated' => true,
             'international' => false,
             'recorded' => true,
          ]),
            'W_0-1' => new Courier([
             'code' => 'WP1',
             'friendlyName' => 'Whistl 0-1kg Packages',
             'maxWeight' => 900,
             'maxLength' => 26,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => 10,
             'serviceCost' => 2,
             'availableServices' => ['standard'],
             'integrated' => true,
             'international' => false,
             'recorded' => true,
          ]),
           'H 0-2' => new Courier([
             'code' => 'H',
             'friendlyName' => 'MyHermes 3-5 Day 0-2kg',
             'maxWeight' => 1850,
             'maxLength' => 120,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 3,
             'availableServices' => ['standard'],
             'integrated' => true,
             'international' => false,
             'recorded' => true,
          ]),
          'H 3-15' => new Courier([
             'code' => 'H',
             'friendlyName' => 'MyHermes 3-5 Day 3-15kg',
             'maxWeight' => 14500,
             'maxLength' => 120,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 5,
             'availableServices' => ['standard'],
             'integrated' => true,
             'international' => false,
             'recorded' => true,
          ]),
          'W48' => new Courier([
             'code' => 'W48',
             'friendlyName' => 'Whistl 48',
             'maxWeight' => 29500,
             'maxLength' => 150,
             'splitWeight' => 26000,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 6,
             'availableServices' => ['standard'],
             'integrated' => true,
             'international' => false,
             'recorded' => true,
          ]),
          'H L&L' => new Courier([
             'code' => 'H',
             'friendlyName' => 'MyHermes 3-5 Day Light & Large',
             'maxWeight' => 29500,
             'maxLength' => 240,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 7,
             'availableServices' => ['standard'],
             'integrated' => true,
             'international' => false,
             'recorded' => true,
          ]),
          'W24' => new Courier([
             'code' => 'W24',
             'friendlyName' => 'Whistl 24',
             'maxWeight' => 29500,
             'maxLength' => 150,
             'splitWeight' => 26000,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 8,
             'availableServices' => ['standard', 'expedited'],
             'integrated' => true,
             'international' => false,
             'recorded' => true,
          ]),
          'T' => new Courier([
             'code' => 'T',
             'friendlyName' => 'Tuffnells',
             'maxWeight' => 42000,
             'maxLength' => 350,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 9,
             'availableServices' => ['standard', 'expedited'],
             'integrated' => false,
             'international' => false,
             'recorded' => true,
          ]),
          'SFP' => new Courier([
             'code' => 'SFP',
             'friendlyName' => 'Prime Logistics',
             'maxWeight' => 23000,
             'maxLength' => 150,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 1,
             'availableServices' => ['sfp'],
             'integrated' => false,
             'international' => false,
             'recorded' => true,
          ]),
          'INT' => new Courier([
             'code' => 'INT',
             'friendlyName' => 'International',
             'maxWeight' => null,
             'maxLength' => null,
             'splitWeight' => null,
             'minOrderValue' => null,
             'maxOrderValue' => null,
             'serviceCost' => 1,
             'availableServices' => null,
             'integrated' => false,
             'international' => true,
             'recorded' => true,
          ]),
       ];
    }

    public function getItemInfo($sku)
    {
        $sql = 'SELECT * FROM products WHERE sku=?';
        $query = $this->handles['products']->prepare($sql);
        $result = $query->execute([$sku]);
        if (!$result) {
            return false;
        }
        $rawItem = $query->fetch(\PDO::FETCH_ASSOC);
        if ($rawItem) {
            return $rawItem;
        }

        return false;
    }

    public function getAllSurchargeZones()
    {
        $query = $this->handles['products']->prepare(
            'SELECT rowID, * FROM exclusions'
        );
        $query->execute();
        $rawSurchargeList = $query->fetchall(\PDO::FETCH_ASSOC);

        return $rawSurchargeList;
    }

    public function getSurcharge(
        $postCode,
        $courier,
        $weight,
        array $previousZones = [],
        $switchZone = null
        ) {
        /*// Uncomment to disable surcharge checker
        return [
            'surcharge' => 0,
            'courier'   => $courier
        ];
        */
        $originalCourier = $courier;
        $surcharge = 0;
        $boundsCourier = null;
        $surchargeFound = false;
        $postCodeArea = substr(str_replace(' ', '', strtoupper($postCode)), 0, -3);
        $rawSurchargeList = $this->getAllSurchargeZones();
        $results = [];
        foreach ($rawSurchargeList as $courierZone) {
            if ((is_null($switchZone) && strpos($courierZone['zone'], 'S') !== 0) || $courierZone['zone'] == $switchZone) {
                if ($courierZone['courier'] == $courier) {
                    $thisZone = $courierZone['courier'].'_'.$courierZone['zone'];
                    $postCodes = json_decode($courierZone['postCodes']);
                    if ($this->toolBox->strrkey($postCodes, $postCodeArea) !== false) {
                        if (!in_array($thisZone, $previousZones)) {
                            $previousZones[] = $thisZone;
                            $surcharge = $courierZone['surcharge'];
                            if ((int) $surcharge) {
                                $surchargeFound = true;
                                $results[] = [
                                    'courier' => $courier,
                                    'surcharge' => $surcharge,
                                ];
                            }
                            if ($courierZone['switchCourier']) {
                                $switchCourierLogic = json_decode($courierZone['switchCourier'], true);
                                $switch = false;
                                if (is_array($switchCourierLogic)) {
                                    $boundsCourier = $this->toolBox->getBounds($weight, $switchCourierLogic);
                                    if (!is_null($boundsCourier)) {
                                        $courier = $boundsCourier;
                                    }
                                } else {
                                    if ($courierZone['switchCourier']) {
                                        $courier = $courierZone['switchCourier'];
                                    }
                                }
                                if (strpos($courierZone['switchCourier'], ':') !== false) {
                                    $switchCourierZone = explode(':', $courierZone['switchCourier']);
                                    $courier = $switchCourierZone[0];
                                    $switchZone = "$switchCourierZone[1]";
                                    $switch = true;
                                }
                                if ($switch) {
                                    $recurseResults = $this->getSurcharge(
                                        $postCode,
                                        $courier,
                                        $weight,
                                        $previousZones,
                                        $switchZone
                                    );
                                } else {
                                    $recurseResults = $this->getSurcharge(
                                        $postCode,
                                        $courier,
                                        $weight,
                                        $previousZones
                                    );
                                }
                                if (!is_null($recurseResults)) {
                                    if ((int) $recurseResults['surcharge']) {
                                        $results[] = $recurseResults;
                                        $surchargeFound = true;
                                    }
                                    $previousZones = $recurseResults['previousZones'];
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!$surchargeFound) {
            return;
        }
        $surcharge = false;
        $courier = null;
        foreach ($results as $result) {
            if (is_null($courier) || ($result['surcharge'] !== false)) {
                if ($surcharge === false || $result['surcharge'] < $surcharge) {
                    $courier = $result['courier'];
                    $surcharge = $result['surcharge'];
                }
            }
        }
        if (!$surcharge) {
            $surcharge = 0;
        }
        if (is_null($courier)) {
            $courier = $originalCourier;
        }

        return [
            'previousZones' => $previousZones,
            'courier' => $courier,
            'surcharge' => $surcharge,
        ];
    }

    public function getSurchargeZone($rowID)
    {
        $query = $this->handles['products']->prepare(
            'SELECT rowID, * FROM exclusions WHERE rowID=?'
        );
        $query->execute([$rowID]);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function getProducts($limit, $page)
    {
        $page = $page - 1;
        $lastRowID = $limit * $page;
        if ($lastRowID < 0) {
            $lastRowID = 0;
        }
        $products = $this->handles['products']->query(
            "SELECT * FROM products
             WHERE rowid > $lastRowID
             ORDER BY rowid
             LIMIT $limit",
            \PDO::FETCH_ASSOC
        );
        $productsArray = [];
        foreach ($products as $product) {
            $productsArray[] = $product;
        }

        return $productsArray;
    }

    // ???
    public function getAllProducts()
    {
        $products = $this->handles['products']->query(
            'SELECT * FROM products',
            \PDO::FETCH_ASSOC
        );
        $productsArray = [];
        foreach ($products as $product) {
            $productsArray[] = $product;
        }

        return $productsArray;
    }

    public function queryProducts($queryTerm)
    {
        $query = $this->handles['products']->prepare(
            'SELECT * FROM products
             WHERE sku LIKE :query
             OR title LIKE :query
             ORDER BY rowid'
        );
        $wildcardQuery = "%{$queryTerm}%";
        $query->bindParam(':query', $wildcardQuery);
        $query->execute();

        return $query->fetchall(\PDO::FETCH_ASSOC);
    }

    public function countProducts()
    {
        return $this->handles['products']->query(
            'SELECT Count(*) FROM products'
        )->fetchColumn();
    }

    public function setItemInfo($sku, $title, $weight, $length, $tags)
    {
        $insert = [
            ':sku' => $sku,
            ':title' => $title,
            ':weight' => (float) $weight,
            ':length' => (float) $length,
            ':tags' => $tags,
        ];
        $query = $this->handles['products']->prepare(
            'INSERT OR REPLACE INTO products (
                    rowID,
                    sku,
                    title,
                    weight,
                    length,
                    tags
            )
            VALUES (
                    (
                            SELECT rowID
                            FROM products
                            WHERE sku = :sku
                    ),
                    :sku,
                    :title,
                    :weight,
                    :length,
                    :tags
            )'
        );
        $executed = $query->execute($insert);

        return $executed;
    }

    public function importLogicCSV($csv)
    {
        $csv = str_getcsv($csv, "\n");
        $headers = [];
        $csvArray = [];
        foreach ($csv as $key => $row) {
            $row = str_getcsv($row, ',');
            if ($key) {
                $csvArray[$key] = [];
                foreach ($row as $hkey => $cell) {
                    @$csvArray[$key][$headers[$hkey]] = $cell;
                }
            } else {
                $headers = $row;
            }
        }
        $success = true;
        foreach ($csvArray as $row) {
            $title = '';
            $foundItem = $this->getItemInfo($row['SKU']);
            if ($foundItem !== false) {
                $title = $foundItem['title'];
            }
            $executed = $this->setItemInfo(
                $row['SKU'],
                // $row['Title'],
                $title,
                $row['Weight'],
                $row['Length'],
                $row['Courier Override']
            );
            if (!$executed) {
                $success = false;
            }
        }

        return $success;
    }

    public function setSurcharge($courier, $zone, $rawPostCodes, $surcharge, $switchCourier)
    {
        $postCodes = json_encode(
            array_map(
                'trim',
                explode(',', $rawPostCodes)
            )
        );
        $insert = [
            ':courier' => $courier,
            ':zone' => $zone,
            ':postCodes' => $postCodes,
            ':surcharge' => (float) $surcharge,
            ':switchCourier' => $switchCourier,
        ];
        $query = $this->handles['products']->prepare(
            'INSERT OR REPLACE INTO exclusions (
                    rowID,
                courier,
                zone,
                postCodes,
                surcharge,
                switchCourier
            )
            VALUES (
                    (
                            SELECT rowID
                            FROM exclusions
                            WHERE courier = :courier
                            AND zone = :zone
                    ),
                  :courier,
                  :zone,
                  :postCodes,
                  :surcharge,
                  :switchCourier
            )'
        );
        $executed = $query->execute($insert);

        return $executed;
    }

    public function removeSurcharge($rowID)
    {
        $query = $this->handles['products']->prepare(
            'DELETE FROM exclusions WHERE rowID=?'
        );

        return $query->execute([$rowID]);
    }

    public function measureOrder(array $orderContent)
    {
        $weight = 0;
        $length = 0;
        $postagePrice = 0;
        foreach ($orderContent['items'] as $item) {
            $product = $this->getItemInfo($item['SKU']);
            if (!$product) {
                $product = $item;
            } else {
                $weight += (float) (
                    $product['weight'] *
                    $item['quantity']
                );
                if ($product['length'] > $length) {
                    $length = (float) $product['length'];
                }
            }
            $postagePrice += $item['shipping'];
        }
        $orderContent['weight'] = $weight;
        $orderContent['length'] = $length;
        $orderContent['postagePrice'] = $postagePrice;
        $orderContent['recordedRequired'] = false;

        return $orderContent;
    }

    public function recheckPrime(array $orderContent)
    {
        if (strtolower($orderContent['service']) == 'sfp') {
            $orderContent['recordedRequired'] = true;
            $this->amazonSFP->setOrders([$orderContent]);
            $options = $this->amazonSFP->getEligibleShippingServices($orderContent['orderID']);
            $metaOrder = new AccessMeta($orderContent['orderID'], $this->accessOrders);
            $metaOrderContents = $metaOrder->metaOrder;
            if (isset($_GET['ddd'])) {
                var_dump(((int) $orderContent['weight'] * 1000) > $this->couriers['SFP']->maxWeight);
            }
            if (
              (
                isset($metaOrderContents['ShipmentServiceLevelCategory']) &&
                strtolower($metaOrderContents['ShipmentServiceLevelCategory']) == 'standard'
              ) ||
              !in_array('prime-premium-uk-mfn', $options) ||
              ((int) $orderContent['weight'] * 1000) > $this->couriers['SFP']->maxWeight
            ) {
                if (isset($metaOrderContents['ShipmentServiceLevelCategory'])) {
                    if (strtolower($metaOrderContents['ShipmentServiceLevelCategory']) == 'standard') {
                        $orderContent['service'] = 'standard';
                    } else {
                        $orderContent['service'] = 'expedited';
                    }
                }
            }
            if (isset($_GET['ddd'])) {
                echo $orderContent['service'];
            }
        }

        return $orderContent;
    }

    public function cheapestAvailableCourier(array $orderContent, array $couriers)
    {
        usort($couriers, ['FespMVC\Model\Courier', 'compare']);
        $selectedCourier = new Courier();
        if ($orderContent['length'] || $orderContent['weight'] || strtolower($orderContent['service']) != 'standard') {
            foreach ($couriers as $courier) {
                $orderIsInternational = !in_array($orderContent['shipping']['countryCode'], Courier::$domesticCountries);
                if (
                    (is_null($courier->maxWeight) || !is_null($courier->splitWeight) || (($orderContent['weight'] * 1000) < $courier->maxWeight)) &&
                    (is_null($courier->maxLength) || (($orderContent['length'] * 100) <= $courier->maxLength)) &&
                    (is_null($courier->minOrderValue) || ($orderContent['total'] > $courier->minOrderValue)) &&
                    (is_null($courier->maxOrderValue) || ($orderContent['total'] < $courier->maxOrderValue)) &&
                    (is_null($courier->availableServices) || in_array(strtolower($orderContent['service']), $courier->availableServices)) &&
                    ($orderIsInternational == $courier->international) &&
                    (!$orderContent['recordedRequired'] || $courier->recorded)
                ) {
                    $selectedCourier = $courier;
                    break;
                }
            }
        }

        return $selectedCourier;
    }

    public function getCourier(array $orderContent, $integrated = true, $skipSurcharge = false)
    {
        //DEBUG
        // file_put_contents('FespMVC/Debug_OrderLogic/orderContent/orderContent.php', json_encode($orderContent, true)."\n", FILE_APPEND);

        $orderContent = $this->measureOrder($orderContent);

        //DEBUG
        // file_put_contents('FespMVC/Debug_OrderLogic/orderContent(post_measureOrder)/orderContent(post_measureOrder).php', json_encode($orderContent, true)."\n", FILE_APPEND);

        $orderContent = $this->recheckPrime($orderContent);

        //DEBUG
        // file_put_contents('FespMVC/Debug_OrderLogic/orderContent(post_recheckPrime)/orderContent(post_recheckPrime).php', json_encode($orderContent, true)."\n", FILE_APPEND);

        $selectedCourier = $this->cheapestAvailableCourier($orderContent, $this->couriers);
        $parcelCount = 1;
        if (!is_null($selectedCourier->splitWeight)) {
            if (((int) $orderContent['weight'] * 1000) > $selectedCourier->maxWeight) {
                $parcelCount = ceil(($orderContent['weight'] * 1000) / $selectedCourier->splitWeight);
            }
        }

        // file_put_contents('FespMVC/Debug_OrderLogic/selectedCourier_' .time(). '.php', var_export($selectedCourier, true));//DEBUG

        $courierCode = $selectedCourier->code;

        $surchargeInfo = $this->getSurcharge($orderContent['shipping']['postCode'], $selectedCourier->code, $orderContent['weight']);
        if (!is_null($surchargeInfo)) {
            $courierCode = $surchargeInfo['courier'];
            if ($surchargeInfo['surcharge']) {
                $surcharge = $surchargeInfo['surcharge'] * $parcelCount;
                if ($orderContent['postagePrice'] < $surcharge) {
                    if ($skipSurcharge) {
                        $selectedCourier = $this->couriers['Y24'];
                    } else {
                        $courierCode = "SUR-$selectedCourier->code-$surcharge";
                        $integrated = false;
                    }
                }
            }
        }

        if ($integrated && $selectedCourier->integrated && $parcelCount === 1 && $courierCode != 'N/A') {
            $courierCode .= 'I';
        }


        $return = [
            'Courier' => $courierCode,
            'Parcels' => $parcelCount,
            'Weight' => $orderContent['weight'],
            'Length' => $orderContent['length'],
        ];


        // file_put_contents('FespMVC/Debug_OrderLogic/return_' .time(). '.php', var_export($return, true));//DEBUG

        return [
            'Courier' => $courierCode,
            'Parcels' => $parcelCount,
            'Weight' => $orderContent['weight'],
            'Length' => $orderContent['length'],
        ];
    }
}
