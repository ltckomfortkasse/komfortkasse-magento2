# Komfortkasse-Extension für Magento 2 und Adobe Commerce

Die Komfortkasse-Extension für Magento 2 und Adobe Commerce (Magento Open Source, früher Magento Community Edition; Adobe Commerce, früher Magento Commerce bzw. Magento Enterprise Edition) automatisiert den Zahlungsabgleich für **Vorkasse, Rechnung und Nachnahme**. Komfortkasse ordnet eingehende Überweisungen automatisch den offenen Bestellungen Ihres Shops zu und setzt den Bestellstatus, sobald das Geld da ist. Die Extension verbindet den Magento-Shop mit Komfortkasse; den Abgleich mit dem Bankkonto übernimmt Komfortkasse. Sie wird per Composer als Paket `ltc/komfortkasse` installiert (Magento-Modul `Ltc_Komfortkasse`) und läuft mit Magento Open Source und Adobe Commerce ab 2.4.

[Komfortkasse für Magento 2 und Adobe Commerce](https://komfortkasse.eu/shopsysteme/adobe-commerce/) · [Kostenlos registrieren](https://ssl.komfortkasse.eu/public/register.jsf) · [Installationsanleitung](https://komfortkasse.freshdesk.com/support/solutions/articles/14000136680) · [Hilfe & Dokumentation](https://komfortkasse.freshdesk.com/support/solutions/14000075022) · [Extension im Adobe Commerce Marketplace](https://commercemarketplace.adobe.com/ltc-komfortkasse.html)

Andere Magento-Versionen: Für Magento 1 gibt es das veraltete Repository [komfortkasse-magento](https://github.com/ltckomfortkasse/komfortkasse-magento).

## Eckdaten

| Merkmal | Angabe |
|---|---|
| Shopsystem | [Magento Open Source](https://business.adobe.com/products/commerce/magento/open-source.html) (früher Magento Community Edition) und [Adobe Commerce](https://business.adobe.com/products/commerce.html) (früher Magento Commerce bzw. Magento Enterprise Edition), auch Adobe Commerce Cloud |
| Unterstützte Versionen | Magento Open Source und Adobe Commerce ab 2.4; ältere Magento-2-Versionen nur, wenn das Composer-Paket `symfony/polyfill-php80` installiert ist (siehe Voraussetzungen) |
| Zahlungsarten | Vorkasse (Vorauskasse, Banküberweisung), Rechnung (Kauf auf Rechnung), Nachnahme |
| Aufgabe der Extension | Überträgt offene Bestellungen an Komfortkasse und setzt den Bestellstatus bei Zahlungseingang oder Storno |
| Paket | Composer-Paket [`ltc/komfortkasse`](https://packagist.org/packages/ltc/komfortkasse) (Typ `magento2-module`, Lizenz GPL-3.0), Magento-Modul `Ltc_Komfortkasse` |
| Bezugsquelle | [Packagist](https://packagist.org/packages/ltc/komfortkasse) und [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com/ltc-komfortkasse.html), dort unter dem Namen „Komfortkasse Offline Payment Automation“ |
| Für wen | Betreiber von Magento-2- und Adobe-Commerce-Shops und Agenturen, die solche Shops betreuen |
| Kosten | Die Extension ist kostenlos. Für Komfortkasse gelten die [Preise](https://komfortkasse.eu/preise/) auf komfortkasse.eu. Auch im Adobe Commerce Marketplace ist die Extension kostenlos. |
| Anbieter | LTC Information Services GmbH, [komfortkasse.eu](https://komfortkasse.eu/) |

## Was ist Komfortkasse?

Komfortkasse ist ein Online-Dienst der LTC Information Services GmbH, der Zahlungen per Banküberweisung für Onlineshops automatisiert. Komfortkasse ruft die Zahlungseingänge vom Bankkonto ab, ordnet sie den offenen Bestellungen zu und meldet den Zahlungsstatus an den Shop zurück. Der Dienst richtet sich an Online-Händler in Deutschland, Österreich und der Schweiz.

Komfortkasse unterstützt die Zahlungsarten, bei denen das Geld per Überweisung kommt:

- [Vorkasse](https://komfortkasse.eu/loesungen/vorkasse/): Der Käufer überweist vor dem Versand. Komfortkasse prüft den Zahlungseingang und setzt den Bestellstatus im Shop automatisch.
- [Rechnung](https://komfortkasse.eu/loesungen/rechnung/): Die Ware wird sofort geliefert, der Käufer überweist danach. Komfortkasse überwacht die Zahlung und sendet bei Bedarf Zahlungserinnerungen und Mahnungen.
- [Nachnahme](https://komfortkasse.eu/loesungen/nachnahme/): Der Versanddienstleister kassiert bei der Zustellung und überweist den Betrag. Komfortkasse überwacht diese Überweisung.

### So funktioniert Komfortkasse mit Magento 2 und Adobe Commerce

1. Die Komfortkasse-Extension überträgt die offenen Bestellungen aus Magento an Ihren Komfortkasse-Account.
2. Komfortkasse ist mit Ihrem Bankkonto verbunden ([unterstützte Banken](https://komfortkasse.eu/banken/)) und ordnet eingehende Zahlungen den Bestellungen zu.
3. Die Extension setzt die Bestellung in Magento auf den gewählten Status, z. B. „Zahlung eingegangen“, oder auf „storniert“, wenn keine Zahlung kommt.

Komfortkasse versendet außerdem Zahlungserinnerungen an Käufer, behandelt Teil- und Überzahlungen und wickelt Rückerstattungen ab. Alle Funktionen sind auf [komfortkasse.eu](https://komfortkasse.eu/) beschrieben.

In Magento gehört jeder Bestellstatus zu einem Bestellzustand (State), z. B. der Status „Processing“ (`processing`) zum Zustand `processing`. Die Komfortkasse-Extension richtet sich nach dem Zustand des gewählten Status: Bei `processing`, `complete` oder `closed` erstellt sie bei Zahlungseingang bei Bedarf die Rechnung (Invoice) und markiert sie als bezahlt, bei `canceled` storniert sie die Bestellung mit der Storno-Funktion von Magento. Einzelheiten stehen unter „Häufige Fragen“.

## Funktionen der Komfortkasse-Extension für Magento 2 und Adobe Commerce

- **Bestellungen übertragen:** Die Extension exportiert offene Bestellungen nach Zahlungsart (Vorkasse, Rechnung, Nachnahme) und Bestellstatus. Als offen gilt eine Bestellung, deren Magento-Zahlungsart in den Einstellungen der jeweiligen Zahlungsart gewählt ist und deren Status zu den dort gewählten offenen Status gehört, unabhängig vom Bestelldatum. Bei Vorkasse gelten zusätzlich Bestellungen mit einer noch nicht bezahlten Rechnung (Invoice im Zustand „Pending“) als offen. Je Bestellung übergibt die Extension unter anderem Bestellnummer, Bestelldatum, Gesamtbetrag, Währung und Wechselkurs, Code der Zahlungsart, Kunden-ID, E-Mail-Adresse, Sprachcode, Rechnungs- und Lieferadresse mit Telefonnummer, Rechnungsnummern und Rechnungsdatum, die ID der Store View sowie die Artikelnummern (SKU, ersatzweise die Artikelnamen).
- **Bestellstatus setzen:** Bei Zahlungseingang setzt die Extension die Bestellung auf den Status, den Sie unter „Status Paid“ der Zahlungsart gewählt haben. Gehört dieser Status zum Zustand `processing`, `complete` oder `closed`, erstellt die Extension eine Rechnung (Invoice), falls die Bestellung noch keine hat, und markiert die Rechnungen der Bestellung als bezahlt. Bei Storno bzw. ausbleibender Zahlung setzt sie den dafür gewählten Status; gehört er zum Zustand `canceled`, storniert sie die Bestellung zuvor mit der Storno-Funktion von Magento, sofern Magento das zulässt. Die Komfortkasse-Transaktions-ID schreibt die Extension als Kommentar in den Bestellverlauf. Bestellungen, die nicht mehr in einem der offenen Status stehen, etwa weil sie von Hand bearbeitet wurden, lässt sie unverändert. Einzige Ausnahme ist eine Bestellung im Storno-Status, für die doch noch Geld eingeht: Für sie lässt die Extension den Wechsel auf den Status für bezahlt zu.
- **Rückerstattungen aus Gutschriften:** Die Extension stellt Komfortkasse die Gutschriften (Credit Memos) bereit, die zu einer Bestellung mit einer der eingetragenen Zahlungsarten gehören und noch keine Transaktions-ID haben: Nummer der Gutschrift, Bestellnummer, Datum und Betrag. Ist im Komfortkasse-Account die Erstellung von Rückerstattungen aus dem Shopsystem aktiviert, führt Komfortkasse daraus die Rückerstattung aus. Danach speichert die Extension die Komfortkasse-ID als Transaktions-ID der Gutschrift und schreibt den Status mit der ID als Kommentar in die Gutschrift; eine Gutschrift mit Transaktions-ID übergibt sie nicht erneut.
- **PDF-Rechnung:** Komfortkasse kann die PDF-Datei einer Rechnung (Invoice) über die Rechnungsnummer abrufen. Ist die Extension Mageplaza PDF Invoice (`Mageplaza_PdfInvoice`) aktiv, liefert die Komfortkasse-Extension deren PDF, sonst das Rechnungs-PDF von Magento.
- **Bestellung zur Rechnungsnummer finden:** Komfortkasse kann eine Bestellung auch über die Nummer einer Magento-Rechnung (Invoice) abrufen statt über die Bestellnummer.
- **Echtzeit-Übertragung:** Komfortkasse liest offene Bestellungen regelmäßig aus dem Shop aus. Zusätzlich meldet die Extension eine Bestellung sofort an Komfortkasse, wenn sie angelegt wird oder ihr Status wechselt und der neue Status zu den offenen Status ihrer Zahlungsart gehört, etwa eine Bestellung auf Rechnung beim Wechsel auf „Complete“. Dazu nutzt sie die Magento-Events `sales_order_place_after`, `sales_order_load_after` und `sales_order_save_after`; Code-Ergänzungen im Shop sind nicht nötig. Die Extension sendet die Meldung nur per HTTPS an `ssl.komfortkasse.eu`, mit den verschlüsselten Bestelldaten und einem Zeitlimit von zwei Sekunden. Sie prüft das Zertifikat, folgt keinen Weiterleitungen und weicht nie auf unverschlüsseltes HTTP aus. Schlägt die Meldung fehl, wird die Bestellung trotzdem gespeichert; Komfortkasse übernimmt sie dann beim nächsten regelmäßigen Abruf.
- **Verschlüsselte Übertragung:** Die Extension überträgt die Daten verschlüsselt, bevorzugt mit OpenSSL (RSA). Die Schlüssel setzt Komfortkasse beim Verbinden des Shops automatisch.
- **Geschützter Aufruf:** Komfortkasse ruft die Extension über `<Shop-URL>/komfortkasse/main/index/action/<Aktion>` auf, die Frontend-Route `komfortkasse` aus `etc/frontend/routes.xml`. Bestelldaten, Gutschriften und PDF-Rechnungen gibt die Extension nur gegen den Zugriffscode heraus; auch Bestellungen und Gutschriften ändert sie nur mit diesem Code. Ohne Zugriffscode beantwortet sie nur das Verbinden (`init`) und das Auslesen der nicht vertraulichen Einstellungen (`readconfig`: Schalter, Zahlungsarten, Status und Verschlüsselungsverfahren); unbekannte Aktionen beantwortet sie mit einer allgemeinen Fehlermeldung. Den Zugriffscode speichert die Extension nur als MD5-Hash, und einen bereits gesetzten Code überschreibt sie beim Verbinden nicht. Für diese Route schaltet sie die CSRF-Prüfung von Magento ab, damit Komfortkasse die Schnittstelle von außen aufrufen kann.
- **Mehrere Websites und Store Views:** Alle Einstellungen lassen sich für die Standardkonfiguration, je Website und je Store View festlegen. Die Extension liest die offenen Bestellungen aller Store Views aus und wendet je Store View deren Einstellungen an, auch „Export Orders“ und „Update Orders“. Die Verbindungsdaten speichert sie beim Verbinden in der Standardkonfiguration (Default Config).
- **Events für eigene Module:** Vor und nach jeder Statusänderung durch Komfortkasse löst die Extension die Events `komfortkasse_change_order_status_before` und `komfortkasse_change_order_status_after` aus, mit der Bestellung, dem neuen Status und der Komfortkasse-Transaktions-ID. Eigene Module können darauf reagieren.
- **Vorhandene Zahlungsarten nutzen:** Die Extension arbeitet mit den Zahlungsarten des Shops und fügt im Checkout keine eigene Zahlungsart hinzu. In den Einstellungen wählen Sie die Zahlungsarten aus der Liste aller Magento-Zahlungsarten aus, ab Werk für Vorkasse „Bank Transfer Payment“ (`banktransfer`).

## Voraussetzungen

- Magento Open Source oder Adobe Commerce ab 2.4, auch Adobe Commerce Cloud. Die Extension nutzt die PHP-Funktion `str_starts_with()` und braucht daher PHP 8 oder das Composer-Paket `symfony/polyfill-php80`. Magento 2.4 erfüllt das: Die Versionen, die noch PHP 7 zulassen, bringen das Paket über ihre Abhängigkeiten mit. Ältere Magento-2-Versionen laufen nicht mit PHP 8 und kommen daher nur mit diesem Paket in Frage.
- Ein Komfortkasse-Account ([kostenlos registrieren](https://ssl.komfortkasse.eu/public/register.jsf))
- Ein Bankkonto, das Sie mit Komfortkasse verbinden ([unterstützte Banken](https://komfortkasse.eu/banken/)), oder ein integriertes Bankkonto von Komfortkasse
- Aktive Zahlungsarten im Shop für die Zahlungsarten, die Komfortkasse übernehmen soll, z. B. „Bank Transfer Payment“ (`banktransfer`) für Vorkasse oder „Cash On Delivery“ (`cashondelivery`) für Nachnahme. Eine Zahlungsart für Kauf auf Rechnung bringt Magento nicht mit; sie kommt z. B. aus einer anderen Extension.
- Empfohlen: PHP mit OpenSSL-Erweiterung, für die Verschlüsselung und für die Echtzeit-Übertragung per HTTPS. Ohne OpenSSL (und ohne die veraltete mcrypt-Erweiterung) überträgt die Extension die Daten nur Base64-kodiert; das ist keine Verschlüsselung.
- Für die Echtzeit-Übertragung zusätzlich: `allow_url_fopen` in der PHP-Konfiguration aktiv und ausgehende HTTPS-Verbindungen vom Server zu `ssl.komfortkasse.eu`
- Die Adresse `<Shop-URL>/komfortkasse/main/index/` muss für Komfortkasse aus dem Internet erreichbar sein.
- Zugriff auf die Kommandozeile des Servers für Composer und `bin/magento`

## Installation in Magento 2 und Adobe Commerce

Die Installation besteht aus vier Schritten: die Extension per Composer installieren, das Modul in Magento aktivieren, die Einstellungen prüfen und den Shop mit dem Komfortkasse-Account verbinden.

1. **Extension installieren:** Im Hauptverzeichnis von Magento `composer require ltc/komfortkasse` ausführen. Composer lädt das Paket von [Packagist](https://packagist.org/packages/ltc/komfortkasse); den Quellcode enthält dieses Repository. Alternativ bestellen Sie die Extension „Komfortkasse Offline Payment Automation“ kostenlos im [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com/ltc-komfortkasse.html) und installieren sie mit den Zugangsschlüsseln Ihres Marketplace-Accounts über `repo.magento.com`; der Paketname ist derselbe. Ohne Composer kopieren Sie den Inhalt dieses Repositorys nach `app/code/Ltc/Komfortkasse`.
2. **Modul aktivieren:** `bin/magento module:enable Ltc_Komfortkasse` und `bin/magento setup:upgrade` ausführen, im Produktionsmodus danach `bin/magento setup:di:compile`, zuletzt `bin/magento cache:clean`. Den allgemeinen Ablauf beschreibt Adobe unter [Erweiterungen von Drittanbietern verwalten](https://experienceleague.adobe.com/de/docs/commerce-operations/installation-guide/tutorials/extensions). Auf Adobe Commerce Cloud fügen Sie das Paket stattdessen mit `composer require ltc/komfortkasse --no-update` und `composer update` hinzu und übertragen `composer.json` und `composer.lock` per Git; der Deploy-Prozess aktiviert das Modul ([Adobe: Erweiterungen verwalten](https://experienceleague.adobe.com/de/docs/commerce-on-cloud/user-guide/configure-store/extensions)).
3. **Einstellungen prüfen:** Im Admin-Bereich unter Stores → Configuration → Sales → Sales den Bereich „Komfortkasse“ öffnen. „Export Orders“ und „Update Orders“ müssen auf „Yes“ stehen. Für Vorkasse („Prepayment“), Rechnung („Invoice“) und Nachnahme („COD“) jeweils wählen: unter „Payment Methods“ die Zahlungsarten des Shops (leer lassen, wenn Komfortkasse eine Zahlungsart nicht übernehmen soll), unter „Statuses Open“ die Status offener Bestellungen (mehrere möglich), unter „Status Paid“ den Status bei Zahlungseingang und im letzten Feld den Status bei Storno bzw. ausbleibender Zahlung. Bei mehreren Websites oder Store Views wählen Sie den Bereich oben links unter „Scope“.
4. **Shop verbinden:** Im Komfortkasse-Händlerbereich „Magento 2 / Adobe Commerce“ als Shopsystem wählen, die URL des Shops eintragen und speichern, dann im Aktionsmenü ≡ die Aktion „Verbindung herstellen“ wählen ([Erste Schritte: Einrichtung und Verbindung zum Shop](https://komfortkasse.freshdesk.com/support/solutions/articles/14000114082)). Die Felder „Encryption“, „Access code“, „API key“, „Secret Key 1“ und „Secret Key 2“ füllt Komfortkasse dabei automatisch; sie dürfen nicht geändert werden.

Nach der Installation stehen in den Einstellungen diese Standardwerte (Konfigurationspfade unter `sales/komfortkasse/`):

| Einstellung | Pfad | Standardwert |
|---|---|---|
| Export Orders | `activate_export` | Yes |
| Update Orders | `activate_update` | Yes |
| Prepayment: Payment Methods | `payment_methods` | `banktransfer` (Bank Transfer Payment) |
| Prepayment: Statuses Open | `status_open` | `pending` (Pending) |
| Prepayment: Status Paid | `status_paid` | `processing` (Processing) |
| Prepayment: Status Cancelled | `status_cancelled` | `canceled` (Canceled) |
| Invoice: Payment Methods | `payment_methods_invoice` | leer |
| Invoice: Statuses Open | `status_open_invoice` | `complete` (Complete) |
| Invoice: Status Paid | `status_paid_invoice` | leer |
| Invoice: Status no payment/debt collection | `status_cancelled_invoice` | leer |
| COD: Payment Methods | `payment_methods_cod` | leer |
| COD: Statuses Open | `status_open_cod` | `processing` (Processing) |
| COD: Status Paid | `status_paid_cod` | leer |
| COD: Status Payment unresolved | `status_cancelled_cod` | leer |

Rechnung und Nachnahme sind ab Werk also nicht aktiv. Erst wenn Sie unter „Invoice: Payment Methods“ bzw. „COD: Payment Methods“ Zahlungsarten wählen, überträgt die Extension solche Bestellungen. Wählen Sie dann auch die Status für bezahlt und für ausbleibende Zahlung; ist dort nichts gewählt, ändert die Extension die Bestellung nicht. Die Installationsanleitung nennt für Rechnung und Nachnahme als übliche Werte „Complete“ für offene, „Closed“ für bezahlte Bestellungen und für ausbleibende Zahlung einen eigenen Status wie „Überfällig“.

Ausführliche Anleitung mit Screenshots: [Installationsanleitung Magento 2 / Adobe Commerce](https://komfortkasse.freshdesk.com/support/solutions/articles/14000136680)

## Update

Für ein Update laden Sie die neue Version per Composer und führen danach die üblichen Magento-Befehle aus. Die Einstellungen und die Verbindung zu Komfortkasse bleiben dabei erhalten, denn die Extension speichert sie in der Magento-Konfiguration (Tabelle `core_config_data`) und nicht in ihren Dateien.

1. Datenbank und Code des Shops sichern.
2. Im Hauptverzeichnis von Magento `composer update ltc/komfortkasse` ausführen. Bei einer Installation ohne Composer die Dateien in `app/code/Ltc/Komfortkasse` durch die neue Version ersetzen.
3. `bin/magento setup:upgrade` ausführen, im Produktionsmodus danach `bin/magento setup:di:compile` und `bin/magento setup:static-content:deploy`.
4. `bin/magento cache:clean` ausführen.

## Häufige Fragen

### Was kostet die Komfortkasse-Extension für Magento 2 und Adobe Commerce?

Die Extension ist kostenlos. Für Komfortkasse selbst fällt eine prozentuale Gebühr pro Zahlungseingang an, ohne Grundgebühr und ohne Einrichtungsgebühr. Die aktuellen Sätze stehen auf der [Preisseite](https://komfortkasse.eu/preise/). Neue Accounts können Komfortkasse kostenlos testen.

### Ist die Komfortkasse-Extension eine Zahlungsart?

Nein. Die Komfortkasse-Extension fügt im Checkout keine Zahlungsart hinzu. Sie arbeitet mit den Zahlungsarten, die der Magento-Shop bereits nutzt, zum Beispiel „Bank Transfer Payment“ (`banktransfer`) für Vorkasse oder „Cash On Delivery“ (`cashondelivery`) für Nachnahme. Welche Zahlungsarten die Extension berücksichtigt, wählen Sie in den Einstellungen unter „Prepayment: Payment Methods“, „Invoice: Payment Methods“ und „COD: Payment Methods“.

### Was passiert, wenn die Bestellnummer fehlt oder der Betrag abweicht?

Komfortkasse ordnet Zahlungen fehlertolerant zu. Die Zuordnung funktioniert auch, wenn die Bestellnummer im Verwendungszweck fehlt, der Name des Zahlers nicht zum Besteller passt oder der Betrag abweicht. Auch Teil- und Überzahlungen behandelt Komfortkasse.

### Was passiert, wenn ein Käufer nicht zahlt?

Komfortkasse sendet automatisch Zahlungserinnerungen an den Käufer. Bleibt die Zahlung aus, setzt die Extension die Bestellung in Magento auf den Status, den Sie dafür gewählt haben. Das ist bei Vorkasse „Prepayment: Status Cancelled“ (ab Werk `canceled`), bei Rechnung „Invoice: Status no payment/debt collection“ und bei Nachnahme „COD: Status Payment unresolved“. Gehört der Status zum Zustand `canceled`, storniert die Extension die Bestellung mit der Storno-Funktion von Magento, sofern Magento das zulässt. Für Rechnung und Nachnahme empfiehlt die Installationsanleitung statt einer Stornierung einen eigenen Status wie „Überfällig“.

### Welche Banken unterstützt Komfortkasse?

Komfortkasse arbeitet mit Ihrem eigenen Bankkonto oder mit einem integrierten Bankkonto von Komfortkasse. Welche Banken sich anbinden lassen, zeigt die Liste der [unterstützten Banken](https://komfortkasse.eu/banken/).

### Wie markiert Komfortkasse eine Bestellung in Magento als bezahlt?

Die Komfortkasse-Extension setzt bei Zahlungseingang den Status, den Sie unter „Status Paid“ der Zahlungsart gewählt haben, ab Werk für Vorkasse „Processing“ (`processing`). Gehört der Status zum Zustand `processing`, `complete` oder `closed`, erstellt die Extension außerdem eine Rechnung (Invoice), falls die Bestellung noch keine hat, und markiert die Rechnungen der Bestellung als bezahlt. Die Komfortkasse-Transaktions-ID steht danach als Kommentar im Bestellverlauf und an der Rechnung. Steht die Bestellung schon auf dem gewählten Status, ändert die Extension sie nicht.

### Unterstützt die Komfortkasse-Extension mehrere Websites und Store Views?

Ja. Die Einstellungen der Komfortkasse-Extension lassen sich wie andere Magento-Einstellungen für die Standardkonfiguration, je Website und je Store View festlegen, und die Extension liest die offenen Bestellungen aller Store Views aus. Je Bestellung übergibt sie die ID der Store View (`store_id`). In Komfortkasse können Sie die Bestellungen aller Store Views in einem Shop zusammenfassen oder für einzelne Magento-Shops eigene Komfortkasse-Shops anlegen; beide Wege beschreibt die [Installationsanleitung](https://komfortkasse.freshdesk.com/support/solutions/articles/14000136680) im Abschnitt „Subshops“.

### Wo finde ich die Komfortkasse-Extension im Adobe Commerce Marketplace?

Im Adobe Commerce Marketplace heißt die Komfortkasse-Extension „Komfortkasse Offline Payment Automation“ ([Eintrag im Marketplace](https://commercemarketplace.adobe.com/ltc-komfortkasse.html)). Sie ist dort kostenlos und für Magento Open Source, Adobe Commerce und Adobe Commerce Cloud angegeben. Der Composer-Paketname ist derselbe wie auf Packagist: `ltc/komfortkasse`.

### Müssen für die Komfortkasse-Extension Dateien von Magento geändert werden?

Nein. Die Komfortkasse-Extension ist ein eigenes Magento-Modul (`Ltc_Komfortkasse`) und liegt nach der Installation per Composer unter `vendor/ltc/komfortkasse`. Die Schnittstelle zu Komfortkasse ist ein eigener Controller unter der Frontend-Route `komfortkasse`, neue Bestellungen und Statusänderungen erkennt die Extension über Magento-Events (`etc/events.xml`), und ihre Einstellungen legt sie über `etc/adminhtml/system.xml` im Bereich Sales an. Eigene Datenbanktabellen legt sie nicht an; Einstellungen und Verbindungsdaten stehen in der Magento-Konfiguration (Tabelle `core_config_data`).

### Gibt es die Komfortkasse-Extension auch für Magento 1?

Ja, aber als eigenes, veraltetes Modul im Repository [komfortkasse-magento](https://github.com/ltckomfortkasse/komfortkasse-magento); Informationen stehen auf der Seite [Komfortkasse für Magento](https://komfortkasse.eu/shopsysteme/magento/). Dieses Repository enthält nur die Extension für Magento 2 und Adobe Commerce.

### Gibt es Komfortkasse auch für andere Shopsysteme?

Ja. Komfortkasse gibt es unter anderem für Shopify, Shopware, WooCommerce, PrestaShop, Gambio und xt:Commerce. Alle Systeme stehen in der [Übersicht der Shopsysteme](https://komfortkasse.eu/shopsysteme/), weitere Plugin-Repositories unter [github.com/ltckomfortkasse](https://github.com/ltckomfortkasse).

## Support

Fragen zur Einrichtung beantworten die [Hilfe-Seiten](https://komfortkasse.freshdesk.com/support/solutions/14000075022) und der [Komfortkasse-Support](https://komfortkasse.eu/kontakt/).

## English

**Komfortkasse extension for Magento 2 and Adobe Commerce (Magento Open Source, formerly Magento Community Edition, and Adobe Commerce, formerly Magento Commerce or Magento Enterprise Edition).** Komfortkasse automatically matches incoming bank transfers to open orders in your shop and updates the order status as soon as the payment has arrived. It supports prepayment by bank transfer (Vorkasse), invoice and cash on delivery. Komfortkasse is a service of LTC Information Services GmbH for online merchants in Germany, Austria and Switzerland.

The extension supports Magento Open Source and Adobe Commerce 2.4 or later, including Adobe Commerce Cloud; older Magento 2 versions only with the Composer package `symfony/polyfill-php80`. It does not add a payment method to the checkout; it works with the shop's existing payment methods. The extension is free of charge; Komfortkasse itself is a paid service ([pricing](https://komfortkasse.eu/preise/)). In the settings you select the Magento payment methods for prepayment, invoice and cash on delivery (by default `banktransfer` for prepayment) and the order statuses for open, paid and cancelled orders. When a payment arrives, the extension creates the invoice if needed, marks it as paid and sets the configured order status; credit memos can be turned into refunds by Komfortkasse.

Installation: run `composer require ltc/komfortkasse` (package from Packagist, also available free of charge in the [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com/ltc-komfortkasse.html) as "Komfortkasse Offline Payment Automation"), then `bin/magento module:enable Ltc_Komfortkasse` and `bin/magento setup:upgrade`, check the settings under Stores → Configuration → Sales → Sales → Komfortkasse and connect the shop in your Komfortkasse account. More information: [Komfortkasse for Magento 2 and Adobe Commerce](https://komfortkasse.eu/shopsysteme/adobe-commerce/) · [Installation guide (English)](https://komfortkasse.freshdesk.com/support/solutions/articles/14000152616)
