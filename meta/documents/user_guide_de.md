# User Guide für das ElasticExportFashionDE Plugin

<div class="container-toc"></div>

## 1 Bei Fashion.de registrieren

Fashion.de ist ein Preisvergleichsportal für Mode und Lifestyle.
Alle Infos zur Kontaktaufnahme finden Sie [hier](http://www.fashion.de/shops/Fashion-Info/Partner-werden/).

## 2 Das Format FashionDE-Plugin in plentymarkets einrichten

Um dieses Format nutzen zu können, benötigen Sie das Plugin Elastic Export.

Auf der Handbuchseite [Datenformate für Preissuchmaschinen exportieren](https://knowledge.plentymarkets.com/basics/datenaustausch/daten-exportieren#30) werden die einzelnen Formateinstellungen beschrieben.

In der folgenden Tabelle finden Sie spezifische Hinweise zu den Einstellungen, Formateinstellungen und empfohlenen Artikelfiltern für das Format **FashionDE-Plugin**. 
<table>
    <tr>
        <th>
            Einstellung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <td class="th" colspan="2">
            Einstellungen
        </td>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            <b>FashionDE-Plugin</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Bereitstellung
        </td>
        <td>
            <b>URL</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Dateiname
        </td>
        <td>
            Der Dateiname muss auf <b>.csv</b> oder <b>.txt</b> enden, damit Kelkoo die Datei erfolgreich importieren kann.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Artikelfilter
        </td>
    </tr>
    <tr>
        <td>
            Aktiv
        </td>
        <td>
            <b>Aktiv</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Märkte
        </td>
        <td>
            Eine oder mehrere Auftragsherkünfte wählen. Die gewählten Auftragsherkünfte müssen an der Variante aktiviert sein, damit der Artikel exportiert wird.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Formateinstellungen
        </td>
    </tr>
    <tr>
        <td>
            Auftragsherkunft
        </td>
        <td>
            Die Auftragsherkunft wählen, die beim Auftragsimport zugeordnet werden soll.
        </td>        
    </tr>
    <tr>
        <td>
            Vorschautext
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>        
    </tr>
    <tr>
        <td>
            Bild
        </td>
        <td>
            <b>Erstes Bild</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            UVP
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>        
    </tr>
    <tr>
        <td>
            MwSt.-Hinweis
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>        
    </tr>
    <tr>
        <td>
            Artikelverfügbarkeit überschreiben
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>        
    </tr>
</table>

## 3 Übersicht der verfügbaren Spalten
<table>
    <tr>
        <th>
            Spaltenbezeichnung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <td>
            art_nr
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Die <b>ID</b> der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            art_name
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Beschränkung:</b> max. <b>250 Zeichen:</b>.
            <b>Inhalt:</b> Entsprechend der Formateinstellung <b>Artikelname</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_kurztext
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Beschränkung:</b> max. <b>3000 Zeichen:</b>.
            <b>Inhalt:</b> Die <b>Beschreibung</b> des Artikels abhängig der Formateinstellung <b>Beschreibung</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_kategorie
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Der <b>Kategorie-Pfad</b> der Standard-Kategorie für den in den Formateinstellungen definierten <b>Mandanten</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_url
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
			<b>Inhalt:</b> Der <b>URL-Pfad</b> des Artikels abhängig vom gewählten <b>Mandanten</b> und der <b>Auftragsherkunft</b> in den Formateinstellungen.
        </td>        
    </tr>
    <tr>
        <td>
            art_img_url
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
            <b>Beschränkung:</b> <b>Mindestgröße</b> 180 x 240 Pixel.
			<b>Inhalt:</b> URL zu dem Bild gemäß der Formateinstellungen <b>Bild</b>. Variantenbilder werden vor Artikelbildern priorisiert.
        </td>        
    </tr>
    <tr>
        <td>
            waehrung
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Die <b>Währung</b> des hinterlegten Verkaufspreises.
        </td>        
    </tr>
    <tr>
        <td>
            art_preis
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Der <b>Verkaufspreis</b> der Variante, abhängig der Formateinstellung <b>Auftragsherkunft</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_marke
        </td>
        <td>
            <b>Inhalt:</b> Der <b>Name des Herstellers</b> des Artikels. Der <b>Externe Name</b> unter <b>Einstellungen » Artikel » Hersteller</b> wird bevorzugt, wenn vorhanden.
        </td>        
    </tr>
    <tr>
        <td>
            art_farbe
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Der verknüpften <b>Attributswert "Farbe"</b> der Variante. Zu verknüpfen unter <b>Einstellungen » Artikel » Attribute » Attribut bearbeiten » Attributverknüpfung"</b> mit dem Attribut "color" für Amazon.
        </td>        
    </tr>
    <tr>
        <td>
            art_groesse
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
			<b>Inhalt:</b> Der verknüpften <b>Attributswert "Größe"</b> der Variante. Zu verknüpfen unter <b>Einstellungen » Artikel » Attribute » Attribut bearbeiten » Attributverknüpfung"</b> mit dem Attribut "size" für Amazon.
        </td>        
    </tr>
    <tr>
        <td>
            art_versand
        </td>
        <td>
            <b>Inhalt:</b> Entsprechend der Formateinstellung <b>Versandkosten</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_sale_preis
        </td>
        <td>
            <b>Inhalt:</b> Der <b>Angebotspreis</b> der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            art_geschlecht
        </td>
        <td>
            <b>Inhalt:</b> Das <b>Geschlecht</b> des hinterlegten Merkmals an der Variante. Zu konfigurieren unter <b>Einstellungen » Artikel » Merkmale"</b> mit internen Namen "article_gender".
        </td>        
    </tr>
    <tr>
        <td>
            art_grundpreis
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Die <b>Grundpreisinformation</b> im Format "Preis / Einheit" abhängig der Formateinstellung <b>Sprache</b>.
        </td>        
    </tr>
</table>

## 4 Lizenz

Das gesamte Projekt unterliegt der GNU AFFERO GENERAL PUBLIC LICENSE – weitere Informationen finden Sie in der [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-fashion-de/blob/master/LICENSE.md).