# ElasticExportFashionDE plugin user guide

<div class="container-toc"></div>

## 1 Registering with Fashion.de

Fashion.de is a price comparison portal for fashion and lifestyle. Please note that this website is currently only available in German. For further information we suggest you to check the extra steps [here](http://www.fashion.de/shops/Fashion-Info/Partner-werden/).

## 2 Setting up the data format FashionDE-Plugin in plentymarkets

To use this format, you need the Elastic Export plugin.

Follow the instructions given on the [Exporting data formats for price search engines](https://knowledge.plentymarkets.com/en/basics/data-exchange/exporting-data#30) page of the manual to set up FashionDE-Plugin in plentymarkets.

The following table lists details for settings, format settings and recommended item filters for the format **FashionDE-Plugin**.
<table>
    <tr>
        <th>
            Settings
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
        <td class="th" colspan="2">
            Settings
        </td>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            Choose <b>FashionDE-Plugin</b>.
        </td>        
    </tr>
    <tr>
        <td>
            Provisioning
        </td>
        <td>
            Choose <b>URL</b>.
        </td>        
    </tr>
    <tr>
        <td>
            File name
        </td>
        <td>
            The file name must have the ending <b>.csv</b> for fashion.de to be able to import the file successfully.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Item filter
        </td>
    </tr>
    <tr>
        <td>
            Active
        </td>
        <td>
            Choose <b>Active</b>.
        </td>        
    </tr>
    <tr>
        <td>
            Markets
        </td>
        <td>
            Choose one or multiple order referrer. The chosen order referrer has to be active at the variation for the item to be exported.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Format settings
        </td>
    </tr>
    <tr>
        <td>
            Order referrer
        </td>
        <td>
            Choose the order referrer that should be assigned during the order import.
        </td>        
    </tr>
    <tr>
        <td>
            Preview text
        </td>
        <td>
            This option does not affect this format.
        </td>        
    </tr>
    <tr>
        <td>
            Image
        </td>
        <td>
            Choose <b>First image</b>.
        </td>        
    </tr>
    <tr>
        <td>
            RRP
        </td>
        <td>
            This option is not relevant for this format.
        </td>        
    </tr>
    <tr>
        <td>
            MwSt.-Hinweis
        </td>
        <td>
            This option is not relevant for this format.
        </td>        
    </tr>
    <tr>
        <td>
            Override item availabilty
        </td>
        <td>
            This option is not relevant for this format.
        </td>        
    </tr>
</table>

## 3 Overview of available columns
<table>
    <tr>
        <th>
            Column description
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
        <td>
            art_nr
        </td>
        <td>
            <b>Required</b><br>
            The <b>ID</b> of the variation.
        </td>        
    </tr>
    <tr>
        <td>
            art_name
        </td>
        <td>
            <b>Required</b><br>
            <b>Restriction:</b> max. <b>250 characters:</b>.
            According the format setting <b>Item name</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_kurztext
        </td>
        <td>
            <b>Required</b><br>
            <b>Restriction:</b> max. <b>3000 characters:</b>.
            The <b>description</b> of the item depending on the format setting <b>Description</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_kategorie
        </td>
        <td>
            <b>Required</b><br>
            The <b>category path of the default category</b> for the defined client in the format settings.
        </td>        
    </tr>
    <tr>
        <td>
            art_url
        </td>
        <td>
        	<b>Required</b><br>
			The product URL according to the format setting <b>product URL</b> and <b>order referrer</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_img_url
        </td>
        <td>
        	<b>Required</b><br>
            <b>Restriction:</b> <b>Minimum size</b> 180 x 240 pixel.
			URL of the image according to the format setting <b>Image</b>. Variation images are prioritized over item images.
        </td>        
    </tr>
    <tr>
        <td>
            waehrung
        </td>
        <td>
        	<b>Required</b><br>
            The <b>currency</b> of the deposited retail price.
        </td>        
    </tr>
    <tr>
        <td>
            art_preis
        </td>
        <td>
        	<b>Required</b><br>
            The <b>retail price</b> of the variation, depending on the format setting <b>order referrer</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_marke
        </td>
        <td>
            The <b>name of the manufacturer</b> of the item. The <b>external name</b> within the menu <b>Settings » Items » Manufacturer</b> will be preferred if existing.
        </td>        
    </tr>
    <tr>
        <td>
            art_farbe
        </td>
        <td>
            <b>Required</b><br>
            The linked <b>attribute value "color"</b> of the variation. It is linkable within <b>Settings » Item » Attributes » Edit attribute » Attribute link"</b> with the attribute "color" for amazon.
        </td>        
    </tr>
    <tr>
        <td>
            art_groesse
        </td>
        <td>
        	<b>Required</b><br>
			The linked <b>attribute value "size"</b> of the variation. It is linkable within <b>Settings » Item » Attributes » Edit attribute » Attribute link"</b> with the attribute "size" for amazon.
        </td>        
    </tr>
    <tr>
        <td>
            art_versand
        </td>
        <td>
            According to the format setting <b>shipping costs</b>.
        </td>        
    </tr>
    <tr>
        <td>
            art_sale_preis
        </td>
        <td>
            The <b>sales price</b> of the variation.
        </td>        
    </tr>
    <tr>
        <td>
            art_geschlecht
        </td>
        <td>
            The <b>gender</b> of the linked property of the variation. It is configurable within <b>Settings » Item » Properties"</b> with the internal name "article_gender".
        </td>        
    </tr>
    <tr>
        <td>
            art_grundpreis
        </td>
        <td>
        	<b>Required</b><br>
            The <b>base price information</b> in the format "price / unit" depending on the format setting <b>Language</b>.
        </td>        
    </tr>
</table>

## 4 License

This project is licensed under the GNU AFFERO GENERAL PUBLIC LICENSE.- find further information in the [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-fashion-de/blob/master/LICENSE.md).