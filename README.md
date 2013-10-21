form-generator
==============

Generate HTML Forms from an input file, database or form builder. 


Concept
=======

Als Eingabemethode wird eine vom User definierte Tabelle in einer MySQL-Datenbank verwendet.

Es wird unterstützung für die folgenden Datentypen und Formularfelder geboten:

- enum: generiert bei weniger als 5 Optionen eine Radiobutton Gruppe, bei 5 oder mehr ein select Dropdown.

- set: generiert bei weniger als 5 Optionen eine Checkbox Gruppe, bei 5 oder mehr ein Multiselect.

- bool, boolean: generiert eine einzelne Checkbox.

- (tiny-, medium- long-)text: generiert eine Textarea.

- (tiny-, medium- long-)blob: generiert ein File Upload Feld.

- date, datetime, timestamp, time, year: Date generiert ein inputfeld vom Typ "date". Datetime, Timestamp und Time jeweils vom Typ "datetime". Year generiert ein inputfeld vom typ "text" mit der Maximallänge 4, welche durch ein regex-pattern auf Jahreszahlen gefiltert wird.

- (tiny-, small-, medium-, big-)int, float, double, decimal: Generieren jeweils ein Inputfeld vom Typ "number". Für Integer-Typen wird jeweils Max und Min entsprechend des Datentyps gesetzt.

Für alle anderen Datentypen findet eine zusätzliche Überprüfung nach dem Namen des Datenbankfelds statt. Folgende Sonderfälle werden behandelt:

Name enthält:

- password, passwort: Ein Inputfeld vom Typ "password" wird erstellt.

- email, e-mail: Ein Inputfeld vom Typ "emain" wird erstellt.

- url, link: Ein Inputfeld vom Typ "url" wird erstellt.

Für alle anderen Fälle wird ein Inputfeld vom typ "text" erstellt. Ist in der Typendefinition der Datenbank eine maximallänge angegeben, z.B. "varchar(50)", so wird ausserdem ein entsprechendes maxlength-attribut in dem Inputfeld erstellt.

Das generierte Formular kann entweder als Code angezeigt werden oder als Webseite mit einfachen Styles heruntergeladen werden. Die Ausgabe erfolgt in HTML5 und setzt voll auf die neuen Datentypen, ein moderner Browser wird also benötigt. Beim herunterladen als Website werden Links auf mögliche Javascript-Workarounds vorgeschlagen.