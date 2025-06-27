<?php
/**
 * Created By Sameh Khalaf 
 * Created At 27-06-2025
 * Logistics Challenge
 */
namespace App\Services;

use Ramsey\Uuid\Type\Integer;

class OrderDistributionService{

    protected $dialyOrders = array(); //dialy orders details that should be saved everyday

    protected $emplyees = array(); // the Company employees details

    //set Dialy orders details , may be handled from db or any another data source
    public function setDialyOrders() : void{
        $this->dialyOrders = [
            'clientOr1'=> ['clientName' => 'clientOr1' , 'price' => 100 , 'long' => '31.228742' , 'lat' => '30.074924' , 'location' => 'Rod-Elfarag'],
            'clientOr2'=> ['clientName' => 'clientOr2' , 'price' => 200 , 'long' => '31.262720' , 'lat' => '30.139736' , 'location' => 'Bahtim'],
            'clientOr3'=> ['clientName' => 'clientOr3' , 'price' => 300 , 'long' => '31.235768' , 'lat' => '30.044375' , 'location' => 'Eltahrir'],
            'clientOr4'=> ['clientName' => 'clientOr4' , 'price' => 400 , 'long' => '31.229388' , 'lat' => '30.036113' , 'location' => 'Hotel-Kornish'],
            'clientOr5'=> ['clientName' => 'clientOr5' , 'price' => 500 , 'long' => '31.231460' , 'lat' => '30.031524' , 'location' => 'Eldewan-Kornish'],
            'clientOr6'=> ['clientName' => 'clientOr6' , 'price' => 600 , 'long' => '31.206020' , 'lat' => '30.178589' , 'location' => 'Qaluib'],
            'clientOr7'=> ['clientName' => 'clientOr7' , 'price' => 700 , 'long' => '31.175108' , 'lat' => '30.120104' , 'location' => 'Ard-ElLewaa'],
            'clientOr8'=> ['clientName' => 'clientOr8' , 'price' => 800 , 'long' => '31.216507' , 'lat' => '30.086934' , 'location' => 'Embaba'],
            'clientOr9'=> ['clientName' => 'clientOr9' , 'price' => 900 , 'long' => '31.277122' , 'lat' => '30.070357' , 'location' => 'ElAbasia'],
            'clientOr10'=> ['clientName' => 'clientOr10' , 'price' => 1000 , 'long' => '31.212589' , 'lat' => '30.037104' , 'location' => 'ElDoki'],
            'clientOr11'=> ['clientName' => 'clientOr11' , 'price' => 1100 , 'long' => '31.246618' , 'lat' => '30.045128' , 'location' => 'Abdeen'],
        ];
    }

    // set Company employees details , also can be handled from any data source
    public function setEmployeesData() : void{
        $this->emplyees = [
            'sameh1' => ['name' => 'sameh1' , 'homeLong' => '31.260402' , 'homeLat' => '30.140052' , 'location' => 'Near-Bahtim','curEmpLocation'=>'Near-Bahtim'],
            'sameh2' => ['name' => 'sameh2' , 'homeLong' => '31.242018' , 'homeLat' => '30.101131' , 'location' => 'Near-Rod-Elfarag','curEmpLocation'=>'Near-Rod-Elfarag'],
            'sameh3' => ['name' => 'sameh3' , 'homeLong' => '31.233314' , 'homeLat' => '30.033539' , 'location' => 'Near-Qasr-Einy','curEmpLocation'=>'Near-Qasr-Einy'],
        ];
    }

    //calculate the distance in KM between any 2 location coordinates 'lat' and 'long'
    public function calcDistance($lat1, $lon1, $lat2, $lon2) : float {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2)**2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2)**2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    //calculate distance of given orders and given employees
    public function setOrdersDistanceForEmployees($orders,$employees) : Array{

        $orderCalcDistance = array();
        foreach($orders as $orderData){
            foreach($employees as $emplyeeData){
                $ordDist = $this->calcDistance($orderData['lat'] , $orderData['long'] , $emplyeeData['homeLat'] , $emplyeeData['homeLong']);
                $details = $emplyeeData['curEmpLocation']. '-EmpToOrd-' . $orderData['location'];
                $orderCalcDistance[] = [
                    'clientName' => $orderData['clientName'], 'empName' => $emplyeeData['name'], 'distance' => $ordDist ,
                    'latestLat' => $orderData['lat'] , 'latestLong' => $orderData['long'],'mainEmpHome' => $emplyeeData['location'] ,
                    'curEmpLocation' => $emplyeeData['curEmpLocation'],'orderLocation' => $orderData['location'] ,'OrdToEmp' =>  $details
                ];
                
            }
        }
        return $orderCalcDistance;
    }

    //sort given array of orders ascending depending on each order distance to employee
    public function sortOrders($orders) : Array{

        usort($orders, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        return $orders;

    }

    //calculate main loop iterations for given dialyOrders and company employees
    public function mainLoopCount() : Integer{
        $diff = count($this->dialyOrders) % count($this->emplyees);
        $count = (count($this->dialyOrders) >= count($this->emplyees)) ? (((count($this->dialyOrders) - $diff) / count($this->emplyees))  + 1) : 1;
        return $count;
    }
    
        
    // function that will hold all logic and return the assigned orders for company employees
    public function handleOrders() : Array{

        $this->setDialyOrders();
        $this->setEmployeesData();
        $mainCount = $this->mainLoopCount();

        $orders = $this->dialyOrders;
        $emplyees = $this->emplyees;
        $assignedOrders = array();
        $reservedOrders = array();

        for($i = 0; $i < $mainCount; $i++){
            $calculatedOrdersDist = $this->sortOrders($this->setOrdersDistanceForEmployees($orders,$emplyees));
            $tempAssignedEmp = array();
            $calculatedOrdersCount = count($calculatedOrdersDist);
            for($j = 0; $j < $calculatedOrdersCount; $j++) {
                
                $empName = $calculatedOrdersDist[$j]['empName'];
                $ordName = $calculatedOrdersDist[$j]['clientName'];

                if (in_array($empName, $tempAssignedEmp))  continue;// to make sure that we distribute orders on employees in same way

                if (in_array($ordName, $reservedOrders))  continue; // avoid return same order more than one time in the assigned data

                if(count($tempAssignedEmp) > count($emplyees)) break;

                $assignedOrders[]   = $calculatedOrdersDist[$j];
                $tempAssignedEmp[]  = $empName;
                $reservedOrders[]   = $ordName;

                unset($orders[$calculatedOrdersDist[$j]['clientName']]);

                //to redefine the employee location after assign an order 
                $emplyees[$empName]['homeLat']  = $calculatedOrdersDist[$j]['latestLat'];
                $emplyees[$empName]['homeLong'] = $calculatedOrdersDist[$j]['latestLong'];
                $emplyees[$empName]['curEmpLocation'] = $calculatedOrdersDist[$j]['orderLocation'];

            }
        }
        
        return $assignedOrders;
    }
}

?>
