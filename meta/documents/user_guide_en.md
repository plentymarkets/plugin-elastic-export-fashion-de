# ElasticExportFashionDE plugin user guide

<div class="container-toc"></div>

## 1 Registering with Fashion.de

Fashion.de is a price comparison portal for fashion and lifestyle. Please note that this website is currently only available in German. For further information we suggest you to check the extra steps [here](http://www.fashion.de/shops/Fashion-Info/Partner-werden/).

## 2 Setting up the data format FashionDE-Plugin in plentymarkets

By installing this plugin you will receive the export format **FashionDE-Plugin**. Use this format to exchange data between plentymarkets and Fashion.de. It is required to install the plugin **Elastic Export** from the plentyMarketplace first before you can use the format FashionDE-Plugin in plentymarkets.

Once both plugins are installed, you can create the export format FashionDE-Plugin. Refer to the [Elastic Export](https://knowledge.plentymarkets.com/en/basics/data-exchange/elastic-export) page of the manual for further details about the individual format settings.

Creating a new export format:

1. Go to **Data » Elastic export**.
2. Click on **New export**.
3. Carry out the settings as desired. Pay attention to the information given in table 1.
4. **Save** the settings.
→ The export format will be given an ID and it will appear in the overview within the **Exports** tab.

The following table lists details for settings, format settings and recommended item filters for the format **FashionDE-Plugin**.

| **Setting**                                           | **Explanation** | 
| :---                                                  | :--- |
| **Settings**                                          | |
| **Name**                                              | Enter a name. The export format is listed under this name in the overview within the **Exports** tab. |
| **Type**                                              | Select the type **Item** from the drop-down list. |
| **Format**                                            | Select **FashionDE-Plugin**. |
| **Limit**                                             | Enter a number. If you want to transfer more than 9,999 data records to the price search engine, then the output file will not be generated again for another 24 hours. This is to save resources. If more than 9,999 data records are necessary, the setting **Generate cache file** has to be active. |
| **Generate cache file**                               | Place a check mark if you want to transfer more than 9,999 data records to the price search engine. The output file will not be generated again for another 24 hours. We recommend not to activate this setting for more than 20 export formats. This is to save resources. |
| **Provisioning**                                      | Select **URL**. This option generates a token for authentication in order to allow external access. |
| **Token, URL**                                        | If you have selected the option **URL** under **Provisioning**, then click on **Generate token**. The token is entered automatically. When the token is generated under **Token**, the URL is entered automatically. |
| **File name**                                         | The file name must have the ending **.csv** or **.txt** for Fashion.de to be able to import the file successfully. |
| **Item filters**                                      | |
| **Add item filters**                                  | Select an item filter from the drop-down list and click on **Add**. There are no filters set as default. It is possible to add multiple item filters from the drop-down list one after the other.<br/> **Variations** = Select **Transfer all** or **Only transfer main variations**.<br/> **Markets** = Select one market, several or **ALL**.<br/> The availability for all markets selected here has to be saved for the item. Otherwise, the export will not take place.<br/> **Currency** = Select a currency.<br/> **Category** = Activate to transfer the item with its category link. Only items belonging to this category are exported.<br/> **Image** = Activate to transfer the item with its image. Only items with images will be transferred.<br/> **Client** = Select a client.<br/> **Stock** = Select which stocks you want to export.<br/> **Flag 1 - 2** = Select the flag.<br/> **Manufacturer** = Select one, several or **ALL** manufacturers.<br/> **Active** = Only active variations are exported. |
| **Format settings**                                   | |
| **Product URL**                                       | Choose which URL should be transferred to the price comparison portal, the item’s URL or the variation’s URL. Variation SKUs can only be transferred in combination with the Ceres store. |
| **Client**                                            | Select a client. This setting is used for the URL structure. |
| **URL parameter**                                     | Enter a suffix for the product URL if this is required for the export. If you have activated the transfer option for the product URL further up, then this character string will be added to the product URL. |
| **Order referrer**                                    | Choose the order referrer that should be assigned during the order import from the drop-down list. |
| **Marketplace account**                               | Select the marketplace account from the drop-down list. The selected referrer is added to the product URL so that sales can be analysed later. |
| **Language**                                          | Select the language from the drop-down list. |
| **Item name**                                         | Select **Name 1**, **Name 2** or **Name 3**. These names are saved in the **Texts** tab of the item. Enter a number into the **Maximum number of characters (def. Text)** field if desired. This specifies how many characters should be exported for the item name. |
| **Preview text**                                      | This option does not affect this format. |
| **Description**                                       | Select the text that you want to transfer as description.<br/> Enter a number into the **Maximum number of characters (def. text)** field if desired. This specifies how many characters should be exported for the description.<br/> Activate the option **Remove HTML tags** if you want HTML tags to be removed during the export. If you only want to allow specific HTML tags to be exported, then enter these tags into the field **Permitted HTML tags, separated by comma (def. Text)**. Use commas to separate multiple tags. |
| **Target country**                                    | Select the target country from the drop-down list. |
| **Barcode**                                           | Select the ASIN, ISBN or an EAN from the drop-down list. The barcode has to be linked to the order referrer selected above. If the barcode is not linked to the order referrer it will not be exported. |
| **Image**                                             | Select **First image** to export this image. |
| **Image position of the energy efficiency label**     | This option does not affect this format. |
| **Stockbuffer**                                       | This option does not affect this format. |
| **Stock for variations without stock limitation**     | This option does not affect this format. |
| **Stock for variations with no stock administration** | This option does not affect this format. |
| **Live currency conversion**                          | Activate this option to convert the price into the currency of the selected country of delivery. The price has to be released for the corresponding currency. |
| **Retail price**                                      | Select gross price or net price from the drop-down list. |
| **Offer price**                                       | Activate to transfer the offer price. |
| **RRP**                                               | This option does not affect this format. |
| **Shipping costs**                                    | Activate this option if you want to use the shipping costs that are saved in a configuration. If this option is activated, then you are able to select the configuration and the payment method from the drop-down lists.<br/> Activate the option **Transfer flat rate shipping charge** if you want to use a fixed shipping charge. If this option is activated, a value has to be entered in the line underneath. |
| **VAT note**                                          | This option does not affect this format. |
| **Overwrite item availability**                       | This option does not affect this format. |
       
_Tab. 1: Settings for the data format **FashionDE-Plugin**_

## 3 Available columns for the export file

Go to **Data » Elastic export** and open the data format **FashionDE-Plugin** in order to download the export file.

| **Column description** | **Explanation** |
| :---                   | :--- |
| art_nr                 | **Required**<br/> The ID of the variation. |
| art_name               | **Required**<br/> Restriction: max. 250 characters<br/> According to the format setting **Item name**. |
| art_kurztext           | **Required**<br/> Restriction: max. 3000 characters<br/> The description of the item depending on the format setting **Description**. |
| art_kategorie          | **Required**<br/> The category path of the default category for the defined **client** in the format settings. |
| art_url                | **Required**<br/> The product URL according to the format setting **Product URL** and **Order referrer**. |
| art_img_url            | **Required**<br/> Restriction: Minimum size 180 x 240 pixels<br/> URL of the image according to the format setting **Image**. Variation images are prioritised over item images. |
| art_waehrung           | **Required**<br/> The currency of the saved retail price. |
| art_preis              | **Required**<br/> The sales price of the variation. If the RRP was activated in the format settings and is higher than the sales price, the RRP is used here. |
| art_marke              | The **name of the manufacturer** of the item. The **external name** set in the menu **System » Item » Manufacturer** is preferred if existing. |
| art_farbe              | **Required**<br/> The linked attribute value "color" of the variation. It can be linked in the menu **System » Item » Attributes » Edit attribute » Attribute link** with the attribute "color" for Amazon. |
| art_groesse            | **Required**<br/> The linked attribute value "size" of the variation. It can be linked in the menu **System » Item » Attributes » Edit attribute » Attribute link** with the attribute "size" for Amazon. |
| art_versand            | According to the format setting **Shipping costs**. |
| art_sale_preis         | If the format setting **RRP** and/or **Offer price** was activated, the sales price or offer price is used here. |
| art_geschlecht         | The gender of the linked property of the variation. It can be linked in the menu **System » Item » Properties** with the webshop name "article_gender". |
| art_grundpreis         | **Required**<br/> The base price information in the format "price/unit" depending on the format setting **Language**. |

## 4 License

This project is licensed under the GNU AFFERO GENERAL PUBLIC LICENSE.- find further information in the [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-fashion-de/blob/master/LICENSE.md).
