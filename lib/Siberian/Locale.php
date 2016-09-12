<?php
/**
 * Base class for localization
 *
 * @category  Zend
 * @package   Zend_Locale
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Siberian_Locale extends Zend_Locale
{

    /**
     * Class wide Euro Zone Locale Constants
     *
     * @var array $_euroZoneTerritoryDatas
     */
    private static $_euroZoneTerritoryDatas = array(
        'AT' => 'de_AT',
        'BE' => 'nl_BE',
        'BG' => 'bg_BG',
        'CY' => 'el_CY',
        'CZ' => 'cs_CZ',
        'DE' => 'de_DE',
        'DK' => 'da_DK',
        'EE' => 'et_EE',
        'ES' => 'es_ES',
        'FI' => 'fi_FI',
        'FR' => 'fr_FR',
        'GB' => 'en_GB',
        'GR' => 'el_GR',
        'HR' => 'hr_HR',
        'HU' => 'hu_HU',
        'IE' => 'en_IE',
        'IT' => 'it_IT',
        'LT' => 'lt_LT',
        'LU' => 'fr_LU',
        'LV' => 'lv_LV',
        'MT' => 'mt_MT',
        'NL' => 'nl_NL',
        'PL' => 'pl_PL',
        'PT' => 'pt_PT',
        'RO' => 'ro_RO',
        'SE' => 'sv_SE',
        'SI' => 'sl_SI',
        'SK' => 'sk_SK'
    );

    /**
     * Class wide Euro Zone Locale Constants
     *
     * @var array $_regionsData
     */
    private static $_regionsData = array(
        "US" => array(
            "AL" => "Alabama",
            "AK" => "Alaska",
            "AS" => "American Samoa",
            "AZ" => "Arizona",
            "AR" => "Arkansas",
            "AF" => "Armed Forces Africa",
            "AA" => "Armed Forces Americas",
            "AC" => "Armed Forces Canada",
            "AE" => "Armed Forces Europe",
            "AM" => "Armed Forces Middle East",
            "AP" => "Armed Forces Pacific",
            "CA" => "California",
            "CO" => "Colorado",
            "CT" => "Connecticut",
            "DE" => "Delaware",
            "DC" => "District of Columbia",
            "FM" => "Federated States Of Micronesia",
            "FL" => "Florida",
            "GA" => "Georgia",
            "GU" => "Guam",
            "HI" => "Hawaii",
            "ID" => "Idaho",
            "IL" => "Illinois",
            "IN" => "Indiana",
            "IA" => "Iowa",
            "KS" => "Kansas",
            "KY" => "Kentucky",
            "LA" => "Louisiana",
            "ME" => "Maine",
            "MH" => "Marshall Islands",
            "MD" => "Maryland",
            "MA" => "Massachusetts",
            "MI" => "Michigan",
            "MN" => "Minnesota",
            "MS" => "Mississippi",
            "MO" => "Missouri",
            "MT" => "Montana",
            "NE" => "Nebraska",
            "NV" => "Nevada",
            "NH" => "New Hampshire",
            "NJ" => "New Jersey",
            "NM" => "New Mexico",
            "NY" => "New York",
            "NC" => "North Carolina",
            "ND" => "North Dakota",
            "MP" => "Northern Mariana Islands",
            "OH" => "Ohio",
            "OK" => "Oklahoma",
            "OR" => "Oregon",
            "PW" => "Palau",
            "PA" => "Pennsylvania",
            "PR" => "Puerto Rico",
            "RI" => "Rhode Island",
            "SC" => "South Carolina",
            "SD" => "South Dakota",
            "TN" => "Tennessee",
            "TX" => "Texas",
            "UT" => "Utah",
            "VT" => "Vermont",
            "VI" => "Virgin Islands",
            "VA" => "Virginia",
            "WA" => "Washington",
            "WV" => "West Virginia",
            "WI" => "Wisconsin",
            "WY" => "Wyoming"
        ),
        "CA" => array(
            "AB" => "Alberta",
            "BC" => "British Columbia",
            "MB" => "Manitoba",
            "NL" => "Newfoundland and Labrador",
            "NB" => "New Brunswick",
            "NS" => "Nova Scotia",
            "NT" => "Northwest Territories",
            "NU" => "Nunavut",
            "ON" => "Ontario",
            "PE" => "Prince Edward Island",
            "QC" => "Quebec",
            "SK" => "Saskatchewan",
            "YT" => "Yukon Territory"
        ),
        "DE" => array(
            "NDS" => "Niedersachsen",
            "BAW" => "Baden-Württemberg",
            "BAY" => "Bayern",
            "BER" => "Berlin",
            "BRG" => "Brandenburg",
            "BRE" => "Bremen",
            "HAM" => "Hamburg",
            "HES" => "Hessen",
            "MEC" => "Mecklenburg-Vorpommern",
            "NRW" => "Nordrhein-Westfalen",
            "RHE" => "Rheinland-Pfalz",
            "SAR" => "Saarland",
            "SAS" => "Sachsen",
            "SAC" => "Sachsen-Anhalt",
            "SCN" => "Schleswig-Holstein",
            "THE" => "Thüringen"
        ),
        "AT" => array(
            "WI" => "Wien",
            "NO" => "Niederösterreich",
            "OO" => "Oberösterreich",
            "SB" => "Salzburg",
            "KN" => "Kärnten",
            "ST" => "Steiermark",
            "TI" => "Tirol",
            "BL" => "Burgenland",
            "VB" => "Voralberg"
        ),
        "CH" => array(
            "AG" => "Aargau",
            "AI" => "Appenzell Innerrhoden",
            "AR" => "Appenzell Ausserrhoden",
            "BE" => "Bern",
            "BL" => "Basel-Landschaft",
            "BS" => "Basel-Stadt",
            "FR" => "Freiburg",
            "GE" => "Genf",
            "GL" => "Glarus",
            "GR" => "Graubünden",
            "JU" => "Jura",
            "LU" => "Luzern",
            "NE" => "Neuenburg",
            "NW" => "Nidwalden",
            "OW" => "Obwalden",
            "SG" => "St. Gallen",
            "SH" => "Schaffhausen",
            "SO" => "Solothurn",
            "SZ" => "Schwyz",
            "TG" => "Thurgau",
            "TI" => "Tessin",
            "UR" => "Uri",
            "VD" => "Waadt",
            "VS" => "Wallis",
            "ZG" => "Zug",
            "ZH" => "Zürich"
        ),
        "ES" => array(
            "A Coruсa" => "A Coruña",
            "Alava" => "Alava",
            "Albacete" => "Albacete",
            "Alicante" => "Alicante",
            "Almeria" => "Almeria",
            "Asturias" => "Asturias",
            "Avila" => "Avila",
            "Badajoz" => "Badajoz",
            "Baleares" => "Baleares",
            "Barcelona" => "Barcelona",
            "Burgos" => "Burgos",
            "Caceres" => "Caceres",
            "Cadiz" => "Cadiz",
            "Cantabria" => "Cantabria",
            "Castellon" => "Castellon",
            "Ceuta" => "Ceuta",
            "Ciudad Real" => "Ciudad Real",
            "Cordoba" => "Cordoba",
            "Cuenca" => "Cuenca",
            "Girona" => "Girona",
            "Granada" => "Granada",
            "Guadalajara" => "Guadalajara",
            "Guipuzcoa" => "Guipuzcoa",
            "Huelva" => "Huelva",
            "Huesca" => "Huesca",
            "Jaen" => "Jaen",
            "La Rioja" => "La Rioja",
            "Las Palmas" => "Las Palmas",
            "Leon" => "Leon",
            "Lleida" => "Lleida",
            "Lugo" => "Lugo",
            "Madrid" => "Madrid",
            "Malaga" => "Malaga",
            "Melilla" => "Melilla",
            "Murcia" => "Murcia",
            "Navarra" => "Navarra",
            "Ourense" => "Ourense",
            "Palencia" => "Palencia",
            "Pontevedra" => "Pontevedra",
            "Salamanca" => "Salamanca",
            "Santa Cruz de Tenerife" => "Santa Cruz de Tenerife",
            "Segovia" => "Segovia",
            "Sevilla" => "Sevilla",
            "Soria" => "Soria",
            "Tarragona" => "Tarragona",
            "Teruel" => "Teruel",
            "Toledo" => "Toledo",
            "Valencia" => "Valencia",
            "Valladolid" => "Valladolid",
            "Vizcaya" => "Vizcaya",
            "Zamora" => "Zamora",
            "Zaragoza" => "Zaragoza"
        ),
        "FR" => array(
            "1" => "Ain",
            "2" => "Aisne",
            "3" => "Allier",
            "4" => "Alpes-de-Haute-Provence",
            "5" => "Hautes-Alpes",
            "6" => "Alpes-Maritimes",
            "7" => "Ardèche",
            "8" => "Ardennes",
            "9" => "Ariège",
            "10" => "Aube",
            "11" => "Aude",
            "12" => "Aveyron",
            "13" => "Bouches-du-Rhône",
            "14" => "Calvados",
            "15" => "Cantal",
            "16" => "Charente",
            "17" => "Charente-Maritime",
            "18" => "Cher",
            "19" => "Corrèze",
            "2A" => "Corse-du-Sud",
            "2B" => "Haute-Corse",
            "21" => "Côte-d'Or",
            "22" => "Côtes-d'Armor",
            "23" => "Creuse",
            "24" => "Dordogne",
            "25" => "Doubs",
            "26" => "Drôme",
            "27" => "Eure",
            "28" => "Eure-et-Loir",
            "29" => "Finistère",
            "30" => "Gard",
            "31" => "Haute-Garonne",
            "32" => "Gers",
            "33" => "Gironde",
            "34" => "Hérault",
            "35" => "Ille-et-Vilaine",
            "36" => "Indre",
            "37" => "Indre-et-Loire",
            "38" => "Isère",
            "39" => "Jura",
            "40" => "Landes",
            "41" => "Loir-et-Cher",
            "42" => "Loire",
            "43" => "Haute-Loire",
            "44" => "Loire-Atlantique",
            "45" => "Loiret",
            "46" => "Lot",
            "47" => "Lot-et-Garonne",
            "48" => "Lozère",
            "49" => "Maine-et-Loire",
            "50" => "Manche",
            "51" => "Marne",
            "52" => "Haute-Marne",
            "53" => "Mayenne",
            "54" => "Meurthe-et-Moselle",
            "55" => "Meuse",
            "56" => "Morbihan",
            "57" => "Moselle",
            "58" => "Nièvre",
            "59" => "Nord",
            "60" => "Oise",
            "61" => "Orne",
            "62" => "Pas-de-Calais",
            "63" => "Puy-de-Dôme",
            "64" => "Pyrénées-Atlantiques",
            "65" => "Hautes-Pyrénées",
            "66" => "Pyrénées-Orientales",
            "67" => "Bas-Rhin",
            "68" => "Haut-Rhin",
            "69" => "Rhône",
            "70" => "Haute-Saône",
            "71" => "Saône-et-Loire",
            "72" => "Sarthe",
            "73" => "Savoie",
            "74" => "Haute-Savoie",
            "75" => "Paris",
            "76" => "Seine-Maritime",
            "77" => "Seine-et-Marne",
            "78" => "Yvelines",
            "79" => "Deux-Sèvres",
            "80" => "Somme",
            "81" => "Tarn",
            "82" => "Tarn-et-Garonne",
            "83" => "Var",
            "84" => "Vaucluse",
            "85" => "Vendée",
            "86" => "Vienne",
            "87" => "Haute-Vienne",
            "88" => "Vosges",
            "89" => "Yonne",
            "90" => "Territoire-de-Belfort",
            "91" => "Essonne",
            "92" => "Hauts-de-Seine",
            "93" => "Seine-Saint-Denis",
            "94" => "Val-de-Marne",
            "95" => "Val-d'Oise"
        ),
        "RO" => array(
            "AB" => "Alba",
            "AR" => "Arad",
            "AG" => "Argeş",
            "BC" => "Bacău",
            "BH" => "Bihor",
            "BN" => "Bistriţa-Năsăud",
            "BT" => "Botoşani",
            "BV" => "Braşov",
            "BR" => "Brăila",
            "B" => "Bucureşti",
            "BZ" => "Buzău",
            "CS" => "Caraş-Severin",
            "CL" => "Călăraşi",
            "CJ" => "Cluj",
            "CT" => "Constanţa",
            "CV" => "Covasna",
            "DB" => "Dâmboviţa",
            "DJ" => "Dolj",
            "GL" => "Galaţi",
            "GR" => "Giurgiu",
            "GJ" => "Gorj",
            "HR" => "Harghita",
            "HD" => "Hunedoara",
            "IL" => "Ialomiţa",
            "IS" => "Iaşi",
            "IF" => "Ilfov",
            "MM" => "Maramureş",
            "MH" => "Mehedinţi",
            "MS" => "Mureş",
            "NT" => "Neamţ",
            "OT" => "Olt",
            "PH" => "Prahova",
            "SM" => "Satu-Mare",
            "SJ" => "Sălaj",
            "SB" => "Sibiu",
            "SV" => "Suceava",
            "TR" => "Teleorman",
            "TM" => "Timiş",
            "TL" => "Tulcea",
            "VS" => "Vaslui",
            "VL" => "Vâlcea",
            "VN" => "Vrancea"
        ),
        "FI" => array(
            "Lappi" => "Lappi",
            "Pohjois-Pohjanmaa" => "Pohjois-Pohjanmaa",
            "Kainuu" => "Kainuu",
            "Pohjois-Karjala" => "Pohjois-Karjala",
            "Pohjois-Savo" => "Pohjois-Savo",
            "Etelä-Savo" => "Etelä-Savo",
            "Etelä-Pohjanmaa" => "Etelä-Pohjanmaa",
            "Pohjanmaa" => "Pohjanmaa",
            "Pirkanmaa" => "Pirkanmaa",
            "Satakunta" => "Satakunta",
            "Keski-Pohjanmaa" => "Keski-Pohjanmaa",
            "Keski-Suomi" => "Keski-Suomi",
            "Varsinais-Suomi" => "Varsinais-Suomi",
            "Etelä-Karjala" => "Etelä-Karjala",
            "Päijät-Häme" => "Päijät-Häme",
            "Kanta-Häme" => "Kanta-Häme",
            "Uusimaa" => "Uusimaa",
            "Itä-Uusimaa" => "Itä-Uusimaa",
            "Kymenlaakso" => "Kymenlaakso",
            "Ahvenanmaa" => "Ahvenanmaa"
        ),
        "EE" => array(
            "EE-37" => "Harjumaa",
            "EE-39" => "Hiiumaa",
            "EE-44" => "Ida-Virumaa",
            "EE-49" => "Jõgevamaa",
            "EE-51" => "Järvamaa",
            "EE-57" => "Läänemaa",
            "EE-59" => "Lääne-Virumaa",
            "EE-65" => "Põlvamaa",
            "EE-67" => "Pärnumaa",
            "EE-70" => "Raplamaa",
            "EE-74" => "Saaremaa",
            "EE-78" => "Tartumaa",
            "EE-82" => "Valgamaa",
            "EE-84" => "Viljandimaa",
            "EE-86" => "Võrumaa"
        ),
        "LV" => array(
            "LV-DGV" => "Daugavpils",
            "LV-JEL" => "Jelgava",
            "Jēkabpils" => "Jēkabpils",
            "LV-JUR" => "Jūrmala",
            "LV-LPX" => "Liepāja",
            "LV-LE" => "Liepājas novads",
            "LV-REZ" => "Rēzekne",
            "LV-RIX" => "Rīga",
            "LV-RI" => "Rīgas novads",
            "Valmiera" => "Valmiera",
            "LV-VEN" => "Ventspils",
            "Aglonas novads" => "Aglonas novads",
            "LV-AI" => "Aizkraukles novads",
            "Aizputes novads" => "Aizputes novads",
            "Aknīstes novads" => "Aknīstes novads",
            "Alojas novads" => "Alojas novads",
            "Alsungas novads" => "Alsungas novads",
            "LV-AL" => "Alūksnes novads",
            "Amatas novads" => "Amatas novads",
            "Apes novads" => "Apes novads",
            "Auces novads" => "Auces novads",
            "Babītes novads" => "Babītes novads",
            "Baldones novads" => "Baldones novads",
            "Baltinavas novads" => "Baltinavas novads",
            "LV-BL" => "Balvu novads",
            "LV-BU" => "Bauskas novads",
            "Beverīnas novads" => "Beverīnas novads",
            "Brocēnu novads" => "Brocēnu novads",
            "Burtnieku novads" => "Burtnieku novads",
            "Carnikavas novads" => "Carnikavas novads",
            "Cesvaines novads" => "Cesvaines novads",
            "Ciblas novads" => "Ciblas novads",
            "LV-CE" => "Cēsu novads",
            "Dagdas novads" => "Dagdas novads",
            "LV-DA" => "Daugavpils novads",
            "LV-DO" => "Dobeles novads",
            "Dundagas novads" => "Dundagas novads",
            "Durbes novads" => "Durbes novads",
            "Engures novads" => "Engures novads",
            "Garkalnes novads" => "Garkalnes novads",
            "Grobiņas novads" => "Grobiņas novads",
            "LV-GU" => "Gulbenes novads",
            "Iecavas novads" => "Iecavas novads",
            "Ikšķiles novads" => "Ikšķiles novads",
            "Ilūkstes novads" => "Ilūkstes novads",
            "Inčukalna novads" => "Inčukalna novads",
            "Jaunjelgavas novads" => "Jaunjelgavas novads",
            "Jaunpiebalgas novads" => "Jaunpiebalgas novads",
            "Jaunpils novads" => "Jaunpils novads",
            "LV-JL" => "Jelgavas novads",
            "LV-JK" => "Jēkabpils novads",
            "Kandavas novads" => "Kandavas novads",
            "Kokneses novads" => "Kokneses novads",
            "Krimuldas novads" => "Krimuldas novads",
            "Krustpils novads" => "Krustpils novads",
            "LV-KR" => "Krāslavas novads",
            "LV-KU" => "Kuldīgas novads",
            "Kārsavas novads" => "Kārsavas novads",
            "Lielvārdes novads" => "Lielvārdes novads",
            "LV-LM" => "Limbažu novads",
            "Lubānas novads" => "Lubānas novads",
            "LV-LU" => "Ludzas novads",
            "Līgatnes novads" => "Līgatnes novads",
            "Līvānu novads" => "Līvānu novads",
            "LV-MA" => "Madonas novads",
            "Mazsalacas novads" => "Mazsalacas novads",
            "Mālpils novads" => "Mālpils novads",
            "Mārupes novads" => "Mārupes novads",
            "Naukšēnu novads" => "Naukšēnu novads",
            "Neretas novads" => "Neretas novads",
            "Nīcas novads" => "Nīcas novads",
            "LV-OG" => "Ogres novads",
            "Olaines novads" => "Olaines novads",
            "Ozolnieku novads" => "Ozolnieku novads",
            "LV-PR" => "Preiļu novads",
            "Priekules novads" => "Priekules novads",
            "Priekuļu novads" => "Priekuļu novads",
            "Pārgaujas novads" => "Pārgaujas novads",
            "Pāvilostas novads" => "Pāvilostas novads",
            "Pļaviņu novads" => "Pļaviņu novads",
            "Raunas novads" => "Raunas novads",
            "Riebiņu novads" => "Riebiņu novads",
            "Rojas novads" => "Rojas novads",
            "Ropažu novads" => "Ropažu novads",
            "Rucavas novads" => "Rucavas novads",
            "Rugāju novads" => "Rugāju novads",
            "Rundāles novads" => "Rundāles novads",
            "LV-RE" => "Rēzeknes novads",
            "Rūjienas novads" => "Rūjienas novads",
            "Salacgrīvas novads" => "Salacgrīvas novads",
            "Salas novads" => "Salas novads",
            "Salaspils novads" => "Salaspils novads",
            "LV-SA" => "Saldus novads",
            "Saulkrastu novads" => "Saulkrastu novads",
            "Siguldas novads" => "Siguldas novads",
            "Skrundas novads" => "Skrundas novads",
            "Skrīveru novads" => "Skrīveru novads",
            "Smiltenes novads" => "Smiltenes novads",
            "Stopiņu novads" => "Stopiņu novads",
            "Strenču novads" => "Strenču novads",
            "Sējas novads" => "Sējas novads",
            "LV-TA" => "Talsu novads",
            "LV-TU" => "Tukuma novads",
            "Tērvetes novads" => "Tērvetes novads",
            "Vaiņodes novads" => "Vaiņodes novads",
            "LV-VK" => "Valkas novads",
            "LV-VM" => "Valmieras novads",
            "Varakļānu novads" => "Varakļānu novads",
            "Vecpiebalgas novads" => "Vecpiebalgas novads",
            "Vecumnieku novads" => "Vecumnieku novads",
            "LV-VE" => "Ventspils novads",
            "Viesītes novads" => "Viesītes novads",
            "Viļakas novads" => "Viļakas novads",
            "Viļānu novads" => "Viļānu novads",
            "Vārkavas novads" => "Vārkavas novads",
            "Zilupes novads" => "Zilupes novads",
            "Ādažu novads" => "Ādažu novads",
            "Ērgļu novads" => "Ērgļu novads",
            "Ķeguma novads" => "Ķeguma novads",
            "Ķekavas novads" => "Ķekavas novads"
        ),
        "LT" => array(
            "LT-AL" => "Alytaus Apskritis",
            "LT-KU" => "Kauno Apskritis",
            "LT-KL" => "Klaipėdos Apskritis",
            "LT-MR" => "Marijampolės Apskritis",
            "LT-PN" => "Panevėžio Apskritis",
            "LT-SA" => "Šiaulių Apskritis",
            "LT-TA" => "Tauragės Apskritis",
            "LT-TE" => "Telšių Apskritis",
            "LT-UT" => "Utenos Apskritis",
            "LT-VL" => "Vilniaus Apskritis"
        )
    );

    public static function getEuroZoneTerritoryDatas() {
        return self::$_euroZoneTerritoryDatas;
    }

    public static function getRegions() {
        return self::$_regionsData;
    }

    public static function getRegionByTerritory($territory) {
        return isset(self::$_regionsData[$territory]) ? self::$_regionsData[$territory] : array();
    }

}
