   <!DOCTYPE html>
{% load static %}
<html lang="en" dir="ltr">
  <head></head>
      <meta charset="utf-8">
    <title>web sayt</title>
    <meta name="viewport" content="width= device-width, intial-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{% static '/semantic-ui/semantic.min.css' %}">

   <!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=G-CJGY1YX8MM"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-CJGY1YX8MM'); </script>

    var adCode = '';
    adCode = adCode.replace('AD_SIZE', '{{ ad_size }}');
    adCode = adCode.replace('AD_TARGETING', '{{ ad_targeting }}');
    document.write(adCode);
  </script>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5683105721841800"
     crossorigin="anonymous"></script>
     </body>
     </html>
