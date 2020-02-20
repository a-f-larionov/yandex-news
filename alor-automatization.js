//--allow-file-access-from-files --disable-web-security --user-data-dir --disable-features=CrossSiteDocumentBlockingIfIsolating


/*ul.search-list

 li.search-item

 div.document__provider-name
 div.document__time
 div.document__title > a
 */


//YANDEX

var error = false;

var dataTable = [];

function yandexStep1ParseData() {
    $('ul.search-list > li.search-item')
        .each(function (i, el) {

            let smiName, orignial_time, today, mp, d;

            smiName = $(el).find('div.document__provider-name')[0].innerHTML;


            orignial_time = $(el).find('div.document__time')[0].innerHTML;


            today = new Date();
            mp = ' ' + today.getFullYear();

            //	time = '21&nbsp;января&nbsp;в&nbsp;13:46';
            time = orignial_time;
            time = time.split('&nbsp;').join(' ');
            time = time.replace(' в ', ' ');
            time = time.replace('августа', 'August' + mp);
            time = time.replace('сентября', 'September' + mp);
            time = time.replace('октября', 'October' + mp);
            time = time.replace('ноября', 'November' + mp);
            time = time.replace('декабря', 'December' + mp);
            time = time.replace('января', 'January' + mp);
            time = time.replace('февраля', 'February' + mp);
            time = time.replace('марта', 'March' + mp);
            time = time.replace('апреля', 'April' + mp);
            time = time.replace('мая', 'May' + mp);
            time = time.replace('июня', 'June' + mp);
            time = time.replace('июля', 'July' + mp);


            // replace year
            time = time.replace('.18 ', '.2018 ');
            time = time.replace('.19 ', '.2019 ');
            time = time.replace('.20 ', '.2020 ');


            if (time.length == 5) {
                time = 'сегодня ' + time;
            }

            // сегодня в год.месяц.день
            time = time.replace('сегодня', today.getFullYear() + '.' + (today.getMonth() + 1) + '.' + today.getDate());


            // вчера в год.месяц.день
            d = new Date(new Date().getTime() - 1000 * 60 * 60 * 24);
            time = time.replace('вчера', d.getFullYear() + '.' + (d.getMonth() + 1) + '.' + d.getDate());

            // меняет местами день и месяц( это нужно для предыдущего года)
            if (time.indexOf('.', 3) == 5) {
                tmp = time.split(' ');
                t0 = tmp[0].split('.');
                t1 = tmp[1].split(':');
                time = t0[1] + '.' + t0[0] + '.' + t0[2] + ' ' + t1[0] + ':' + t1[1];
            }

            if (new Date(time) == 'Invalid Date') {
                error = true;
                console.log('so... catch error Invalid Date');
                console.log('original', orignial_time);
                console.log(time);
                debug = orignial_time;
            }

            time = new Date(time);


            url = $(el).find('div.document__title > a')[0].href;
            title = $(el).find('div.document__title > a')[0].innerHTML;

//            console.log(time);


            /**
             Финам.ру
             AK&M
             Финмаркет
             Инвестинг.ком
             Банки.рк
             Инвест Форсайт
             Элитный трейдер
             Profinance.ru
             Expert.ru
             */
            var author = '';
            switch (smiName.toLocaleLowerCase()) {
                case 'finam.ru':
                case 'финам.ру':
                case 'финмаркет':
                case 'инвестинг.ком':
                case 'investing.com':
                case 'банки.ру':
                case 'инвест форсайт':
                case 'инвест-форсайт':
                case 'элитный трейдер':
                case 'expert.ru':
                case 'ак&м':
                    author = 'Антонов';
                    break;
            }

            console.log(smiName, author);
            // добавляет на отправку на севрев
            if (true) {
                dataTable.push({
                    smiName: smiName,
                    time: time,
                    url: url,
                    title: title,
                    author: author,
                    encode: 'utf-8'
                });
            }
        });


    dataTable.reverse();


}

function commonGetTableV2() {
    let html, num;
    html = '';

    //html += "<div>";
    html += '<table>';


    dataTable.forEach(function (row) {


        html += '<tr>';

        //html += '<td>'+num+'</td>';
        html += '<td>' + row.title + '</td>';
        html += '<td>' + row.day + '.' + row.month + '.' + '2018' + '</td>';
        html += '<td>' + row.smiName + '</td>';
        html += '<td><a href=' + row.url + '>' + row.url + '</a></td>';

        detected = '-';
        if (row.alorDetected) detected += row.alorDetected;
        if (row.antonovDetected) detected += row.antonovDetected;
        if (row.konuhovaDetected) detected += row.konuhovaDetected;
        if (row.koruhinDetected) detected += row.koruhinDetected;
        if (row.drem1Detected) detected += row.drem1Detected;
        if (row.drem2Detected) detected += row.drem2Detected;

        html += '<td>' + detected + '</td>';

        html += '</tr>';
        num++;
    });

    html += '</table>';
    //html += '</div>';
    html += "<script>alert(" + dataTable.length + ");</script>";
    return html;
}

function commonShowTable() {

    wnd = window.open(location.href);
    wnd.addEventListener('load', function () {
        console.log('draw table');
        wnd.document.body.innerHTML = commonGetTableV2();

    });
}

function step3_analyzeAuthor() {
    let queue;
    let url, wnd, index;

    queue = [];

    dataTable.forEach(function (row, i) {
        queue.push({
            row: row,
            i: i
        });
    });


    var iteration_1 = function () {
        let data;
        if (queue.length) {
            console.log('queue.length : ' + queue.length);
            data = queue.shift();
            url = data.row.url;
            index = data.i;
            iteration_2();
        }
    };

    var iteration_2 = function () {
        console.log('try open: ' + url);
        wnd = window.open(url);
        window.wnd = wnd;
        wnd.addEventListener('load', function () {
            console.log('loaded: ' + url);
            iteration_3();
        });
    }

    var iteration_3 = function () {
        let text;
        text = wnd.document.body.innerHTML;
        text = strip_tags(text);

        alorDetected = antonovDetected = koruhinDetected = konuhovaDetected = drem1Detected = drem2Detected = antipDetected = false;

        if (text.search('Алор') != -1) alorDetected = text.substr(text.search('Алор') - 30, 30 + 60);


        if (text.search('Антонов') != -1) antonovDetected = text.substr(text.search('Антонов') - 30, 30 + 60);
        if (text.search('Корюх') != -1) koruhinDetected = text.substr(text.search('Корюх') - 30, 30 + 60);
        if (text.search('Конюх') != -1) konuhovaDetected = text.substr(text.search('Конюх') - 30, 30 + 60);
        if (text.search('Дрем') != -1) drem1Detected = text.substr(text.search('Дрем') - 30, 30 + 60);
        if (text.search('Дрём') != -1) drem2Detected = text.substr(text.search('Дрём') - 30, 30 + 60);
        if (text.search('Антип') != -1) antipDetected = text.substr(text.search('Антип') - 30, 30 + 60);
        if (text.search('Мустя') != -1) antipDetected = text.substr(text.search('Мустя') - 30, 30 + 60);

        console.log('jkl');

        dataTable[index].alorDetected = alorDetected;

        dataTable[index].antonovDetected = antonovDetected;
        dataTable[index].koruhinDetected = koruhinDetected;
        dataTable[index].konuhovaDetected = konuhovaDetected;
        dataTable[index].drem1Detected = drem1Detected;
        dataTable[index].drem2Detected = drem2Detected;
        dataTable[index].antipDetected = antipDetected;

        console.log('finish It');

        wnd.close();

        iteration_1();
    }


    iteration_1();
}

function strip_tags(str) {	// Strip HTML and PHP tags from a string
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

    return str.replace(/<\/?[^>]+>/gi, '');
}


function yandexOpenAllNews() {
    let n = 0;
    let wnd;
    dataTable.forEach(function (row, index) {
        n++;

        (function (url) {
            setTimeout(function () {
                console.log('open url:' + url);

                // отправка данных на сервер
                if (true) {
                    // wnd = window.open(url);
                }

            }, 1000 + n * 1000);


        })(row.url);

    });
}


//ФинамРу
/*
 структура:
 table.navlist
 div.pl20.pt10
 div.activities-item
 div.activities-item-header
 span.date === date!
 div.mid === title
 a.href
 */

function finamStep1() {
    els = $('div.pl20  div.activities-item');

    for (var n = els.length - 1; n >= 0; n--) {
        let date, title, url;
        el = $(els[n]);

        date = new Date(el.find('span.date').html());

        title = el.find('div.mid').html().replace(/(<([^>]+)>)/ig, "").trim();

        url = el.find('div.mid > a').attr('href')

        dataTable.push({
            smiName: 'Finam.Ru',
            time: date,
            url: 'https://www.finam.ru' + url,
            title: title,
            author: 'Антонов',
            encode: 'windows-1251'
        });
    }
}

//Инвестинг ком

/*
 div.articlesPageShort.arial_11.lightgrayFont
 div.bigTitle
 div
 a === title and url
 div.contentSectionDetails
 span.date
 */

function investingStep1() {
    els = $('div.articlesPageShort > div.bigTitle');


    for (let n = 0; n < els.length; n++) {
        el = els[n];

        url = $(el).find('div > a').attr('href');
        title = $(el).find('div > a').html();

        date = new Date($(el).find('div > div.contentSectionDetails > span.date')
            .html()
            .replace('&nbsp;', '')
            .replace('&nbsp;', '')
            .replace('-', '')
            .split('.')
            .reverse()
            .join('.'));


        dataTable.push({
            smiName: 'Investing.com',
            time: date,
            url: 'https://ru.investing.com' + url,
            title: title,
            author: 'Антонов',
            encode: 'utf-8'
        });
    }
}


function sendToServer(timeout) {


    dataTable.forEach(function (data, index) {

        url = 'http://alor.8ffd246e-5d74-49a5-8696-e92eff606a60.pub.cloud.scaleway.com/alor_store_data.php';
        url += "?time=" + Math.round(data.time.getTime() / 1000),
            url += "&title=" + data.title;
        url += "&url=" + data.url;
        url += "&smiName=" + data.smiName;
        url += "&encode=" + data.encode;
        url += "&author=" + data.author;
        if (timeout) url += "&timeout=" + timeout

        // отправка данных на сервер
        if (true) {
            wnd = window.open(url);
        }
        console.log('store:' + data.time + ' ' + data.title, url);

    });


}


if (location.href.search('www.finam.ru') != -1) {
    finamStep1();
}

if (location.href.search('investing.com') != -1) {
    investingStep1();
}

if (location.href.search('news.yandex.ru') != -1) {
    yandexStep1ParseData();
}

if (!error) {
    sendToServer(3000);
} else {
    console.log('error');
}