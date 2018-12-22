<?php

return [

    /**
     * When the PTV library is used a seperate connection must be made to the PTV database
     * The database can be se specified within the config/database.php file the database connection name must be
     * specificied here. Default is "ptv", but as always you are free to change this to anything you want
     */

    'connection' => 'ptv',

    /**
     * The friendly naming config setting allows you to configure easier naming for specific columns in a specific table
     * this function can be helpfull when writing a import and you need to know what you are importing. With the original
     * column names of PTV that can be tricky.
     * 
     * Remember: for the classes "addOrder", "updateOrder", "deleteOrder" when you need to set a a value for a specific column
     * you can always use the originel column name, the class will figure it out that you want to set that specific column
     * 
     * When friendly names are specified the class expects that those values are used and not the original names
     */

    'friendly_naming' => [
        
        'IMPH_IMPORT_HEADER' => [
            'IMPH_REFERENCE' => 'id',
            'IMPH_EXTID' => 'reference'
        ],

        'IORH_ORDER_HEADER' => [
            'IORH_IMPH_REFERENCE' => 'id',
            'IORH_PRIORITY' => 'priority'
        ],

        'IORA_ORDER_ACTIONPOINT' => [
            'IORA_IMPH_REFERENCE' => 'id',
            'IORA_POSTCODE' => 'postcode',
            'IORA_CITY' => 'city',
            'IORA_COUNTRY' => 'country',
            'IORA_STREET' => 'street',
            'IORA_HOUSENO' => 'houseno',
            'IORA_HANDLINGTIME_CLASS' => 'timewindow',
            'IORA_EARLIEST_DATETIME' => 'from',
            'IORA_LATEST_DATETIME' => 'till'
        ],
    ],

    /**
     * The defaults settings are settings you can use to set default values for column data some have been already filled
     * for you as they are mendatory to be filled on insert. There is NO check if the fields do exist in de databse. 
     * Do NOT edit this if you are not 100% sure what you are doing
     */

    'defaults' => [

        /**
         * Default settings for the table IMPH_IMPORT_HEADER
         */
        
        'IMPH_IMPORT_HEADER' => [
            'IMPH_CONTEXT' => '1',
            'IMPH_OBJECT_TYPE' => 'ORDER',
            'IMPH_ACTION_CODE' => 'NEW',
            'IMPH_PROCESS_CODE' => '10',
            'IMPH_CREATION_TIME' => '%CURRENT_DATE%'
        ],

        /**
         * Default settings for the table IORH_ORDER_HEADER
         */

        'IORH_ORDER_HEADER' => [
            'IORH_ORDER_TYPE' => 'DELIVERY',
            'IORH_CODRIVER_NEEDED' => '0',
            'IORH_SOLO' => '0',
            'IORH_PRIORITY' => '1'
        ],

        /**
         * Default settings for the table IORA_ORDER_ACTIONPOINT
         */

        'IORA_ORDER_ACTIONPOINT' => [
            'IORA_ACTION' => 'DELIVERY',
            'IORA_IS_ONETIME' => '1',
            'IORA_CODRIVER_NEEDED' => '0',
            'IORA_TOUR_POS' => 'NONE'
        ]
    ]
];