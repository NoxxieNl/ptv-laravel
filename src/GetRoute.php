<?php
namespace Noxxie\Ptv;

use Noxxie\Ptv\Models\Exph_export_header;

class GetRoute
{
    /**
     * Get all routes that are not imported
     * $action filter can be specified to filter on specific actions
     *
     * @param string $action
     * @return collection
     */
    public function getNotImported(string $action = null)
    {
        $data = [];

        if (!is_null($action)) {
            if (!in_array($action, ['UPDATE', 'DELETE', 'NEW'])) {
                return null;
            }

            $search = (new Exph_export_header)->NotImported()->withAction($action)->get();
        } else {
            $search = (new Exph_export_header)->NotImported()->get();
        }

        if (is_null($search)) {
            return null;
        }

        foreach ($search as $route)
        {
            $data[] = $this->fetchRoute($route);
        }

        return collect($data);
    }

    /**
     * Fetch a specific route ID
     *
     * @param string $id
     * @return collection
     */
    public function fetch(string $id)
    {
        $data = [];
        $search = (new Exph_export_header)->where('EXPH_EXTID', $id)->get();

        if (is_null($search)) {
            return null;
        }

        foreach ($search as $route)
        {
            $data[] = $this->fetchRoute($route);
        }

        return collect($data);
    }

    /**
     * Update a route as imported
     *
     * @param string $id
     * @return boolean
     */
    public function updateRouteAsImported(string $id)
    {
        $exportHeader = (new Exph_export_header)->find($id);

        // Check if we can find the import header
        if (is_null($exportHeader)) {
            return false;
        }

        // Update the data and return
        $exportHeader->EXPH_PROCESS_CODE = '50';
        $exportHeader->save();

        return true;
    }

    /**
     * Internal helper function to allow easy return of same data when fetching routes
     *
     * @param Exph_export_header $route
     * @return collection
     */
    protected function fetchRoute(Exph_export_header $route)
    {
        $data['id'] = $route->EXPH_REFERENCE;
        $data['routenumber'] = $route->EXPH_EXTID;
        $data['vehicle'] = !is_null($route->etpttourheader) ? $route->etpttourheader->ETPT_VEHICLE_EXTID1 : '';
        $data['date'] = !is_null($route->etpttourheader) ? $route->etpttourheader->ETPT_START_DATETIME : '';
        $data['type'] = $route->EXPH_ACTION_CODE;
        $data['details'] = [];

        if (!is_null($route->etpstourstops)) {
            foreach ($route->etpstourstops as $tourstop) {
                
                foreach($tourstop->etpatouractionpoint()->withoutDepot()->get() as $actionpoint)
                {
                    $data['details'][] = [
                        'reference' => $actionpoint->ETPA_ORDER_EXTID1,
                        'order' => $actionpoint->ETPA_ETPS_TOURPOINT_SEQUENCE,
                        'street' => $actionpoint->ETPA_STREET,
                        'houseno' => $actionpoint->ETPA_HOUSENO,
                        'city' => $actionpoint->ETPA_CITY,
                        'postcode' => $actionpoint->ETPA_POSTCODE
                    ];
                }
            }
        }
        
        return collect($data);
    }
}