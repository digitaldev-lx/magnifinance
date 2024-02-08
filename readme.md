# Readme for Spot-B

# docker

cp envs/.env.qld.encrypted ./ && php artisan env:decrypt --key=g2SQwZDpD4QQgvL5qCfxANEjrnzCa93j --env=qld && rm .env.qld.encrypted

php artisan env:encrypt --key=g2SQwZDpD4QQgvL5qCfxANEjrnzCa93j --env=staging \
&& rm .env.staging 
&& mv .env.staging.encrypted envs/.env.staging.encrypted

docker build --build-arg lara_env=staging -t registry.gitlab.com/digitaldev-lx/spot-b .

docker push registry.gitlab.com/digitaldev-lx/spot-b


```yaml

version: '3.8'
services:

    cloudflared:
        image: cloudflare/cloudflared:latest
        command: tunnel --no-autoupdate run --token eyJhIjoiNDg1NTg4OThmYjVmYWM5MmY2YmEyMjE1ZmYzYmQxY2YiLCJ0IjoiN2Y1ZjczZTAtMjAwNS00ODM3LWEwNTUtODA2NjVkZWQxMzc4IiwicyI6Ik1EaGtaakUzTldZdE5tSmlOQzAwTlRneUxXSXpZVFF0TlRaa09EWm1aVEExTlRGaCJ9

    spot_b_staging:
        image: "registry.gitlab.com/digitaldev-lx/spot-b"
        ports:
            - '3080:80'
        container_name: "spot-b-staging"
        restart: unless-stopped

services:
  cloudflared:
    image: cloudflare/cloudflared:latest
    command: tunnel --no-autoupdate run --token eyJhIjoiNDg1NTg4OThmYjVmYWM5MmY2YmEyMjE1ZmYzYmQxY2YiLCJ0IjoiN2Y1ZjczZTAtMjAwNS00ODM3LWEwNTUtODA2NjVkZWQxMzc4IiwicyI6Ik1EaGtaakUzTldZdE5tSmlOQzAwTlRneUxXSXpZVFF0TlRaa09EWm1aVEExTlRGaCJ9
```

brew install cloudflared &&

sudo cloudflared service install eyJhIjoiNDg1NTg4OThmYjVmYWM5MmY2YmEyMjE1ZmYzYmQxY2YiLCJ0IjoiN2Y1ZjczZTAtMjAwNS00ODM3LWEwNTUtODA2NjVkZWQxMzc4IiwicyI6Ik1EaGtaakUzTldZdE5tSmlOQzAwTlRneUxXSXpZVFF0TlRaa09EWm1aVEExTlRGaCJ9
### Plugins used in the app

<ol>
    <li>
        <strong>Croppie (used for front page silder in superadmin panel) </strong> - <a href=“https://foliotek.github.io/Croppie/”>foliotek.github.io/Croppie/</a>
    </li>    
    <li>
        <strong>FullCalendar</strong> - <a href=“https://fullcalendar.io”>fullcalendar.io</a>
    </li>    
    <li>
        <strong>Select2</strong> - <a href=“https://select2.org/”>select2.org</a>
    </li>    
    <li>
        <strong>Dropify</strong> - <a href=“https://jeremyfagis.github.io/dropify/”>jeremyfagis.github.io/dropify</a>
    </li>    
    <li>
        <strong>DropzoneJS</strong> - <a href=“https://www.dropzonejs.com/”>www.dropzonejs.com</a>
    </li>    
    <li>
        <strong>Summernote</strong> - <a href=“https://summernote.org/”>summernote.org</a>
    </li>
    <li>
        <strong>Datepicker</strong> - <a href=“https://getdatepicker.com/4/”>getdatepicker.com/4</a>
    </li>    
    <li>
        <strong>Timepicker</strong> - <a href=“https://github.com/jdewit/bootstrap-timepicker”>github.com/jdewit/bootstrap-timepicker</a>
    </li>    
    <li>
        <strong>Colorpicker</strong> - <a href=“https://itsjavi.com/bootstrap-colorpicker/”>itsjavi.com/bootstrap-colorpicker</a>
    </li>    
    <li>
        <strong>Date Range Picker</strong> - <a href=“https://github.com/dangrossman/daterangepicker”>github.com/dangrossman/daterangepicker</a>
    </li>    
    <li>
        <strong>Ace Code Editor</strong> - <a href=“https://ace.c9.io/”>ace.c9.io</a>
    </li>    
    <li>
        <strong>chart.js v2.7.3 </strong> - <a href=“https://www.chartjs.org”>www.chartjs.org</a>
    </li>
    <li>
        <strong>bootstrap-tagsinput</strong> - <a href=“https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/”>bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples</a>
    </li>
    <li>
        <strong>SweetAlert</strong> - <a href=“https://sweetalert.js.org/”>sweetalert.js.org</a>
    </li>
    <li>
        <strong>google-reptcha (version2 and version3)</strong>
    </li>
    <li>
        <strong>Sortable | jQuery UI </strong> - <a href=“https://jqueryui.com/sortable/”>jqueryui.com/sortable</a>
    </li>
    <li>
        <strong>Location Picker</strong> - <a href=“https://github.com/Logicify/jquery-locationpicker-plugin”>github.com/Logicify/jquery-locationpicker-plugin</a>
    </li>
    <li>
        <strong>Owl Carousel</strong> - <a href=“https://owlcarousel2.github.io/OwlCarousel2/”>owlcarousel2.github.io/OwlCarousel2</a>
    </li>
    <li>
        <strong>jQuery Lazy (Image Lazy Load on Front)</strong> - <a href=“http://jquery.eisbehr.de/lazy/”>jquery.eisbehr.de/lazy</a>
    </li>
</ol>
