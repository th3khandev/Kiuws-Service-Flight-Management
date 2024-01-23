const countries = [
  {
    code: "AF",
    name: "Afganistán",
  },
  {
    code: "AL",
    name: "Albania",
  },
  {
    code: "AQ",
    name: "Antártida",
  },
  {
    code: "DZ",
    name: "Argelia",
  },
  {
    code: "AS",
    name: "Samoa americana",
  },
  {
    code: "AD",
    name: "Andorra",
  },
  {
    code: "AO",
    name: "Angola",
  },
  {
    code: "AG",
    name: "Antigua y Barbuda",
  },
  {
    code: "AZ",
    name: "Azerbaiyán",
  },
  {
    code: "AR",
    name: "Argentina",
  },
  {
    code: "AU",
    name: "Australia",
  },
  {
    code: "AT",
    name: "Austria",
  },
  {
    code: "BS",
    name: "Bahamas",
  },
  {
    code: "BH",
    name: "Bahrein",
  },
  {
    code: "BD",
    name: "Bangladesh",
  },
  {
    code: "AM",
    name: "Armenia",
  },
  {
    code: "BB",
    name: "Barbados",
  },
  {
    code: "BE",
    name: "Bélgica",
  },
  {
    code: "BM",
    name: "Bermudas",
  },
  {
    code: "BT",
    name: "Bhután",
  },
  {
    code: "BO",
    name: "Bolivia",
  },
  {
    code: "BA",
    name: "Bosnia-Herzegovina",
  },
  {
    code: "BW",
    name: "Botswana",
  },
  {
    code: "BV",
    name: "Bouvet, Isla",
  },
  {
    code: "BR",
    name: "Brasil",
  },
  {
    code: "BZ",
    name: "Belice",
  },
  {
    code: "IO",
    name: "Océano Índico, Territorio británico del",
  },
  {
    code: "SB",
    name: "Salomón, Islas",
  },
  {
    code: "VG",
    name: "Vírgenes británicas, Islas",
  },
  {
    code: "BN",
    name: "Brunei Darussalam",
  },
  {
    code: "BG",
    name: "Bulgaria",
  },
  {
    code: "MM",
    name: "Myanmar",
  },
  {
    code: "BI",
    name: "Burundi",
  },
  {
    code: "BY",
    name: "Belarus",
  },
  {
    code: "KH",
    name: "Camboya",
  },
  {
    code: "CM",
    name: "Camerún",
  },
  {
    code: "CA",
    name: "Canadá",
  },
  {
    code: "CV",
    name: "Cabo Verde",
  },
  {
    code: "KY",
    name: "Caimán, Islas",
  },
  {
    code: "CF",
    name: "Centroafricana, República",
  },
  {
    code: "LK",
    name: "Sri Lanka",
  },
  {
    code: "TD",
    name: "Chad",
  },
  {
    code: "CL",
    name: "Chile",
  },
  {
    code: "CN",
    name: "China",
  },
  {
    code: "TW",
    name: "Taiwan, Provincia de China",
  },
  {
    code: "CX",
    name: "Christmas, isla",
  },
  {
    code: "CC",
    name: "Cocos (Keeling), islas",
  },
  {
    code: "CO",
    name: "Colombia",
  },
  {
    code: "KM",
    name: "Comores",
  },
  {
    code: "YT",
    name: "Mayotte",
  },
  {
    code: "CG",
    name: "Congo",
  },
  {
    code: "CD",
    name: "Congo, República democrática del",
  },
  {
    code: "CK",
    name: "Cook, Islas",
  },
  {
    code: "CR",
    name: "Costa Rica",
  },
  {
    code: "HR",
    name: "Croacia",
  },
  {
    code: "CU",
    name: "Cuba",
  },
  {
    code: "CY",
    name: "Chipre",
  },
  {
    code: "CZ",
    name: "República Checa",
  },
  {
    code: "BJ",
    name: "Benín",
  },
  {
    code: "DK",
    name: "Dinamarca",
  },
  {
    code: "DM",
    name: "Dominica",
  },
  {
    code: "DO",
    name: "República Dominicana",
  },
  {
    code: "EC",
    name: "Ecuador",
  },
  {
    code: "SV",
    name: "El Salvador",
  },
  {
    code: "GQ",
    name: "Guinea ecuatorial",
  },
  {
    code: "ET",
    name: "Etiopía",
  },
  {
    code: "ER",
    name: "Eritrea",
  },
  {
    code: "EE",
    name: "Estonia",
  },
  {
    code: "FO",
    name: "Feroe, Islas",
  },
  {
    code: "FK",
    name: "Falkland (Malvinas), Islas",
  },
  {
    code: "GS",
    name: "Georgia del Sur e Islas Sandwich del Sur",
  },
  {
    code: "FJ",
    name: "Fiji",
  },
  {
    code: "FI",
    name: "Finlandia",
  },
  {
    code: "AX",
    name: "Åland, Islas",
  },
  {
    code: "FR",
    name: "Francia",
  },
  {
    code: "GF",
    name: "Guyana francesa",
  },
  {
    code: "PF",
    name: "Polinesia francesa",
  },
  {
    code: "TF",
    name: "Territorios Australes franceses",
  },
  {
    code: "DJ",
    name: "Djibouti",
  },
  {
    code: "GA",
    name: "Gabón",
  },
  {
    code: "GE",
    name: "Georgia",
  },
  {
    code: "GM",
    name: "Gambia",
  },
  {
    code: "PS",
    name: "Palestina, Territorio ocupado de",
  },
  {
    code: "DE",
    name: "Alemania",
  },
  {
    code: "GH",
    name: "Ghana",
  },
  {
    code: "GI",
    name: "Gibraltar",
  },
  {
    code: "KI",
    name: "Kiribati",
  },
  {
    code: "GR",
    name: "Grecia",
  },
  {
    code: "GL",
    name: "Groenlandia",
  },
  {
    code: "GD",
    name: "Granada",
  },
  {
    code: "GP",
    name: "Guadalupe",
  },
  {
    code: "GU",
    name: "Guam",
  },
  {
    code: "GT",
    name: "Guatemala",
  },
  {
    code: "GN",
    name: "Guinea",
  },
  {
    code: "GY",
    name: "Guayana",
  },
  {
    code: "HT",
    name: "Haití",
  },
  {
    code: "HM",
    name: "Heard y McDonald, Islas",
  },
  {
    code: "VA",
    name: "Santa Sede (Estado de la Ciudad del Vaticano)",
  },
  {
    code: "HN",
    name: "Honduras",
  },
  {
    code: "HK",
    name: "Hong-Kong",
  },
  {
    code: "HU",
    name: "Hungría",
  },
  {
    code: "IS",
    name: "Islandia",
  },
  {
    code: "IN",
    name: "India",
  },
  {
    code: "ID",
    name: "Indonesia",
  },
  {
    code: "IR",
    name: "Irán, República islámica de",
  },
  {
    code: "IQ",
    name: "Iraq",
  },
  {
    code: "IE",
    name: "Irlanda",
  },
  {
    code: "IL",
    name: "Israel",
  },
  {
    code: "IT",
    name: "Italia",
  },
  {
    code: "CI",
    name: "Costa de Marfil",
  },
  {
    code: "JM",
    name: "Jamaica",
  },
  {
    code: "JP",
    name: "Japón",
  },
  {
    code: "KZ",
    name: "Kazajstán",
  },
  {
    code: "JO",
    name: "Jordania",
  },
  {
    code: "KE",
    name: "Kenia",
  },
  {
    code: "KP",
    name: "Corea, República popular democrática de",
  },
  {
    code: "KR",
    name: "Corea, República de",
  },
  {
    code: "KW",
    name: "Kuwait",
  },
  {
    code: "KG",
    name: "Kirguistán",
  },
  {
    code: "LA",
    name: "Laos, República democrática popular de",
  },
  {
    code: "LB",
    name: "Líbano",
  },
  {
    code: "LS",
    name: "Lesotho",
  },
  {
    code: "LV",
    name: "Letonia",
  },
  {
    code: "LR",
    name: "Liberia",
  },
  {
    code: "LY",
    name: "Libia, Jamahiriya árabe",
  },
  {
    code: "LI",
    name: "Liechtenstein",
  },
  {
    code: "LT",
    name: "Lituania",
  },
  {
    code: "LU",
    name: "Luxemburgo",
  },
  {
    code: "MO",
    name: "Macao",
  },
  {
    code: "MG",
    name: "Madagascar",
  },
  {
    code: "MW",
    name: "Malawi",
  },
  {
    code: "MY",
    name: "Malasia",
  },
  {
    code: "MV",
    name: "Maldivas",
  },
  {
    code: "ML",
    name: "Mali",
  },
  {
    code: "MT",
    name: "Malta",
  },
  {
    code: "MQ",
    name: "Martinica",
  },
  {
    code: "MR",
    name: "Mauritania",
  },
  {
    code: "MU",
    name: "Mauricio",
  },
  {
    code: "MX",
    name: "Méjico",
  },
  {
    code: "MC",
    name: "Mónaco",
  },
  {
    code: "MN",
    name: "Mongolia",
  },
  {
    code: "MD",
    name: "Moldavia, República de Moldova",
  },
  {
    code: "ME",
    name: "Montenegro",
  },
  {
    code: "MS",
    name: "Montserrat",
  },
  {
    code: "MA",
    name: "Marruecos",
  },
  {
    code: "MZ",
    name: "Mozambique",
  },
  {
    code: "OM",
    name: "Omán",
  },
  {
    code: "NA",
    name: "Namibia",
  },
  {
    code: "NR",
    name: "Nauru",
  },
  {
    code: "NP",
    name: "Nepal",
  },
  {
    code: "NL",
    name: "Países Bajos",
  },
  {
    code: "CW",
    name: "Curazao",
  },
  {
    code: "AW",
    name: "Aruba",
  },
  {
    code: "SX",
    name: "San Martín (parte holandesa)",
  },
  {
    code: "BQ",
    name: "Bonaire, San Eustaquio y Saba",
  },
  {
    code: "NC",
    name: "Nueva Caledonia",
  },
  {
    code: "VU",
    name: "Vanuatu",
  },
  {
    code: "NZ",
    name: "Nueva Zelanda",
  },
  {
    code: "NI",
    name: "Nicaragua",
  },
  {
    code: "NE",
    name: "Níger",
  },
  {
    code: "NG",
    name: "Nigeria",
  },
  {
    code: "NU",
    name: "Niué",
  },
  {
    code: "NF",
    name: "Norfolk, Isla",
  },
  {
    code: "NO",
    name: "Noruega",
  },
  {
    code: "MP",
    name: "Marianas del Norte, Islas",
  },
  {
    code: "UM",
    name: "Ultramarinas de los Estados Unidos, Islas",
  },
  {
    code: "FM",
    name: "Micronesia, Estados federados de",
  },
  {
    code: "MH",
    name: "Marshall, Islas",
  },
  {
    code: "PW",
    name: "Palau",
  },
  {
    code: "PK",
    name: "Pakistán",
  },
  {
    code: "PA",
    name: "Panamá",
  },
  {
    code: "PG",
    name: "Papúa Nueva Guinea",
  },
  {
    code: "PY",
    name: "Paraguay",
  },
  {
    code: "PE",
    name: "Perú",
  },
  {
    code: "PH",
    name: "Filipinas",
  },
  {
    code: "PN",
    name: "Pitcairn",
  },
  {
    code: "PL",
    name: "Polonia",
  },
  {
    code: "PT",
    name: "Portugal",
  },
  {
    code: "GW",
    name: "Guinea-Bissau",
  },
  {
    code: "TL",
    name: "Timor oriental",
  },
  {
    code: "PR",
    name: "Puerto Rico",
  },
  {
    code: "QA",
    name: "Qatar",
  },
  {
    code: "RE",
    name: "Reunión",
  },
  {
    code: "RO",
    name: "Rumanía",
  },
  {
    code: "RU",
    name: "Federación Rusa",
  },
  {
    code: "RW",
    name: "Ruanda",
  },
  {
    code: "BL",
    name: "San Bartolomé",
  },
  {
    code: "SH",
    name: "Santa Helena",
  },
  {
    code: "KN",
    name: "San Cristóbal y Nieves",
  },
  {
    code: "AI",
    name: "Anguila",
  },
  {
    code: "LC",
    name: "Santa Lucía",
  },
  {
    code: "MF",
    name: "San Martín (parte francesa)",
  },
  {
    code: "PM",
    name: "San Pedro y Miquelón",
  },
  {
    code: "VC",
    name: "San Vicente y Granadinas",
  },
  {
    code: "SM",
    name: "San Marino",
  },
  {
    code: "ST",
    name: "Santo Tomé y Príncipe",
  },
  {
    code: "SA",
    name: "Arabia Saudí",
  },
  {
    code: "SN",
    name: "Senegal",
  },
  {
    code: "RS",
    name: "Serbia",
  },
  {
    code: "SC",
    name: "Seychelles",
  },
  {
    code: "SL",
    name: "Sierra Leona",
  },
  {
    code: "SG",
    name: "Singapur",
  },
  {
    code: "SK",
    name: "Eslovaquia",
  },
  {
    code: "VN",
    name: "Vietnam",
  },
  {
    code: "SI",
    name: "Eslovenia",
  },
  {
    code: "SO",
    name: "Somalia",
  },
  {
    code: "ZA",
    name: "Sudáfrica",
  },
  {
    code: "ZW",
    name: "Zimbabwe",
  },
  {
    code: "ES",
    name: "España",
  },
  {
    code: "SS",
    name: "Sudán del Sur",
  },
  {
    code: "SD",
    name: "Sudán",
  },
  {
    code: "EH",
    name: "Sahara occidental",
  },
  {
    code: "SR",
    name: "Suriname",
  },
  {
    code: "SJ",
    name: "Svalbard e isla Jan Mayen",
  },
  {
    code: "SZ",
    name: "Swazilandia",
  },
  {
    code: "SE",
    name: "Suecia",
  },
  {
    code: "CH",
    name: "Suiza",
  },
  {
    code: "SY",
    name: "Siria, República árabe",
  },
  {
    code: "TJ",
    name: "Tayikistán",
  },
  {
    code: "TH",
    name: "Tailandia",
  },
  {
    code: "TG",
    name: "Togo",
  },
  {
    code: "TK",
    name: "Tokelau",
  },
  {
    code: "TO",
    name: "Tonga",
  },
  {
    code: "TT",
    name: "Trinidad y Tobago",
  },
  {
    code: "AE",
    name: "Emiratos árabes unidos",
  },
  {
    code: "TN",
    name: "Túnez",
  },
  {
    code: "TR",
    name: "Turquía",
  },
  {
    code: "TM",
    name: "Turkmenistán",
  },
  {
    code: "TC",
    name: "Turcos y Caicos, Islas",
  },
  {
    code: "TV",
    name: "Tuvalu",
  },
  {
    code: "UG",
    name: "Uganda",
  },
  {
    code: "UA",
    name: "Ucrania",
  },
  {
    code: "MK",
    name: "Macedonia, antigua república yugoslava de",
  },
  {
    code: "EG",
    name: "Egipto",
  },
  {
    code: "GB",
    name: "Reino Unido",
  },
  {
    code: "GG",
    name: "Guernsey",
  },
  {
    code: "JE",
    name: "Jersey",
  },
  {
    code: "IM",
    name: "Man, Isla de",
  },
  {
    code: "TZ",
    name: "Tanzania, República unida de",
  },
  {
    code: "US",
    name: "Estados Unidos",
  },
  {
    code: "VI",
    name: "Vírgenes (USA), Islas",
  },
  {
    code: "BF",
    name: "Burkina Faso",
  },
  {
    code: "UY",
    name: "Uruguay",
  },
  {
    code: "UZ",
    name: "Uzbekistán",
  },
  {
    code: "VE",
    name: "Venezuela",
  },
  {
    code: "WF",
    name: "Wallis y Futuna",
  },
  {
    code: "WS",
    name: "Samoa",
  },
  {
    code: "YE",
    name: "Yemen",
  },
  {
    code: "ZM",
    name: "Zambia",
  },
];


export default countries;