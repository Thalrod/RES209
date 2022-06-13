
<?php



class Calendar
{

  
    private $_month;
    private $_year;
    public function __construct($month, $year)
    {
        $this->_month = $month;
        $this->_year = $year;
    }


    function renderCalendar()
    {   
    
        $PrevMonth = "Mois précédent";
        $NextMonth = "Mois suivant";
        $CurrentMonth = "Mois en cours";


        $daysOfWeek = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi','Dimanche');
        $firstDayOfMonth = mktime(0, 0, 0, $this->_month, 1, $this->_year);
        $numberDays = date('t', $firstDayOfMonth);
        $dateComponents = getdate($firstDayOfMonth);
        $monthName = $dateComponents['month'];
        $dayOfWeek = ($dateComponents['wday'] + 6) % 7;
        // 0 = Sunday, 6 = Saturday (cf Documentation.txt) so to start on Monday we add 6 Sunday (0 + 6) % 7 = 6 so we get 6
        // and Monday (1 + 6) % 7 = 0 so we get 0
        $dateToday = date("Y-m-d");


        $prevthismonth = date("m", mktime(0, 0, 0, $this->_month - 1, 1, $this->_year));
        $prevthisyear = date("Y", mktime(0, 0, 0, $this->_month - 1, 1, $this->_year));
        $nextthismonth = date("m", mktime(0, 0, 0, $this->_month + 1, 1, $this->_year));
        $nextthisyear = date("Y", mktime(0, 0, 0, $this->_month + 1, 1, $this->_year));

        $calendar = "<h2>" . $monthName. " $this->_year</h2>";



        $calendar .= "<div id='controls'><form method='post' action='?' class='inline'><input type='hidden' name='month' value='" . $prevthismonth . "'><button type='submit' class='btn btn-primary btn-sm' name='year' value='" . $prevthisyear . "' class='link-button'>" . $PrevMonth . "</button></form>";
        $calendar .= "<form method='post' action='?' class='inline'><input type='hidden' name='month' value='" . date("m") . "'><button type='submit' class='btn btn-primary btn-sm' name='year' value='" . date("Y") . "' class='link-button'>" . $CurrentMonth . "</button></form>";
        $calendar .= "<form method='post' action='?' class='inline'><input type='hidden' name='month' value='" . $nextthismonth . "'><button type='submit' class='btn btn-primary btn-sm' name='year' value='" . $nextthisyear . "' class='link-button'>" . $NextMonth . "</button></form></div>";

        $calendar .= "<table class='table table-bordered'>";
        $calendar .= "<tr>";

        foreach ($daysOfWeek as $day) {
            $calendar .= "<th class='header'>$day</th>";
        }

        $calendar .= "</tr><tr>";
        $currentDay = 1;
        if ($dayOfWeek > 0) {
            for ($i = 0; $i < $dayOfWeek; $i++) {
                $calendar .= "<td class='empty' height='80'></td>";
            }
        }

        $month = str_pad($this->_month, 2, "0", STR_PAD_LEFT);

        while ($currentDay <=  $numberDays) {
            if ($dayOfWeek == 7) {
                $dayOfWeek = 0;
                $calendar .= "</tr><tr>";
            }

            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            //date on format DD/MM/YYYY
            $date = "$this->_year-$month-$currentDayRel";
            
            
            $isToday = ($date == $dateToday) ? " today" : "";
            
            $calendar .= "<td class='cell$isToday' id='$date' height='80'><a><h4>$currentDayRel</h4></a></td>";

            $currentDay++;
            $dayOfWeek++;
        }

        if ($dayOfWeek != 7) {
            $remainingDays = 7 - $dayOfWeek;
            for ($i = 0; $i < $remainingDays; $i++) {
                $calendar .= "<td class='empty' height='80'></td>";
            }
        }

        $calendar .= "</tr></table>";

        echo $calendar;
    }
}
?>