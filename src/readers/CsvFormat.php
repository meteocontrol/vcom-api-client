<?php
namespace meteocontrol\client\vcomapi\readers;

class CsvFormat {

    const FORMAT_CSV = "csv";
    const FORMAT_JSON ="json";

    const LINE_BREAK_LF = "LF";
    const LINE_BREAK_CR = "CR";
    const LINE_BREAK_CRLF = "CR/LF";

    const DELIMITER_COMMA = "comma";
    const DELIMITER_SEMICOLON = "semicolon";
    const DELIMITER_COLON = "colon";
    const DELIMITER_TAB = "tab";

    const DECIMAL_POINT_DOT = "dot";
    const DECIMAL_POINT_COMMA = "comma";

    const EMPTY_PLACE_HOLDER_EMPTY = "";

    const PRECISION_0 = 0;
    const PRECISION_1 = 1;
    const PRECISION_2 = 2;
    const PRECISION_3 = 3;
    const PRECISION_4 = 4;
}
