<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/02/2017
 * Time: 2:12 PM
 */
define('PATH_SITE_BASE','C:\\wamp\\www\\allen_frame_trial\\');
include('../system/config/config.php');
$timestamp = time();
echo '<pre>';

// TEST IMAGE ENTITY

// Testing read image file from different source and get image mime from different methods
//$entity = new entity_image();
//$value = [
//    ['name'=>'Image Data URL','source_file'=>'data:image/jpeg;base64,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'],
//    ['name'=>'Wrong Image Data URL','source_file'=>'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAA0JCUlNSMj5aYVpQYEpRUk//2wBDAQ4ODhMREyYVFSZPNS01T09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0//wAARCADIAMgDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwBw8cah/wA+dn+T/wDxVOHja/P/AC52n5P/APFVzSIamSOgi7OjHjK/P/Lnafk//wAVTh4wvj/y6Wn5P/8AFVgrHUqx0w5mbX/CX33/AD6Wv5P/APFUh8YXw/5c7T8n/wDiqx/LprRikK7Ng+M74f8ALnafk/8A8VTf+E1vv+fO0/J//iqw3jqFkxQHMzoT42vv+fO0/J//AIqk/wCE3v8A/nzs/wAn/wDiq5simYoDmZ0x8b3/AGs7P8n/APiqafHOoD/lys/yf/4quapCKA5mdJ/wneo/8+Vn+T//ABVJ/wAJ5qP/AD5WX5P/APFVzRWmbaYuZnUf8J5qH/PlZ/k//wAVTh461A/8uVn+T/8AxVcrt5p6rQHMzqR44v8A/nzs/wAn/wDiqevjW/PWztPyf/4quWC1OgGKQczOk/4TS+yB9jtPyf8A+KpU8ZXzLn7Jafk//wAVXOIm5xTkXAxQHMzo/wDhML7/AJ9LT8n/APiqRvGV8P8Al0tPyf8A+KrBC01lP91sfSgLs3T40vh/y52n5P8A/FU0+N78f8udp+T/APxVYPlluik464HSozDIyl0jdlHVgvAouguzoD44v/8Anzs/yf8A+KornPs85XeIJNmM7tvFFF0F5GiunXYieU27hIzhz/dq5Z6JqF1CJre33xnoc1rvO0nhm4mb5WnfJGaveEb2WSJrVkQRQrkMOprH2kuW5s4K9jn5NIvYLiOCWHbJKPkGetJc2U1nII7hNrEZABzW7p93NqmrSXNwqhbZGC7QT3qp4hO65gcjG6LP3cU4zfPykuCUbmVHCZZVjXALHAJq0ujXEl29sJIw6LuLZ4qKzP8ApkP++K6i3QjUbuQ5ycL29KVWo4vQKcFJanJnTZDYz3RkQJCxUg9W+lE+ivHpX25p1Hy7vL281q3ikaPDbDO65uOeR0zU+quDY6hAG+WGJFA3gis/ay0NPZxOfstEW8tBO9yYskjG3NU7jS3t9RitXYssp+VwOorce2kl0C3hgI3MwZv3mMDPXFMuxG+tWcW5GMSEsQ5P50KrK71B042WhTuNAhVHEE8rSqM7WFZ9lZRzWlxPNvHldNp7+9bCRGG9vbycxpGwwuSeRWdH8nh6eTAzKxx8p7n1oU5W3BwjfYiewjXRRd4bzSobO7jr6U25sI0NrHECHl5Ylq1Jox/ZxtwvIhU/6o/zphQvq8a7T+7i7R+vtSVSQOCsVjaWTb7dEUSouS26qVhAJboLJtKrktnpVqOK+M09zaom1iQWZR0+lOsFMdncXPzcjAIAqrtRepDSbSsR6hDEskYhRFDD+Grt3BCslrFGkYy3zFR1+tR3MbNc2SENkqOuPatZrUSXCTv5u5Pu/OtRKdkrlKN27GVqyxrdKsaooVP4BirsCyxwQRW1vHKjRFpAcAn8az9TffqEuc8ccnNWra5kWGKQ6fPK8SkLIOFxVyT5IkprnZeitx/Ypi8obmUybsdOemauLv8AtES+ZEYfs+Tb4G5jjrWTDqN3POPs9rK6IhUwhhj6mkW9uDqiSpYkzonliLPJ/GsuST3NFOK2LelwMNLdBEf9JMhY4+6B0pjLcRvDb2zRCNIOY34DHufc1Xku9QhuLWL7JJF5QISEN/rSfWi6u7632yT6WElwVEjHsewFPlle/cOZLQTWm2WqIdS8r92P9GUfeoqPUHvGtN9xpMSZUDzmbLAfSit6S90zk7mleuF8NwqD99843f0q34UGyzvpvRf6Utsul3ljbwXk6EIPu+cRg0WMtraaRqaRSouWYRrv5I7fWs4u8bW6lyXvXE0C3lbQ7yWJN0kxIUc80mopAl/ZC8wsQjw+VIHH1pr362Phq2hsrpVuGI37Dyo71S1m7guxbtDKJGVcP16/jTjGTlcHJKNizfPphuLYacULeZ820dvzrWkUpcRp5ZzK5OPKHPH1rkYXCTI7dAwJ4rZk1eybUIJh/qo1IY+X3+nelUpvRLUITXUstGX1u0g2t/o8ZcjaoOabqFpPb6dqckxJM53jG0YGO/8A9aqcOrWkd5d3LqcyDbEBGDx/SqlvfQxaVPbTb2lmJIO0NjPuelT7OQ+eJoz3c1nFp0ELECTAYFl6Y7en41VWNF8RzyKcYjBYmYDn61Xu9TgmvbWZEcRwDkFFBNM/teJLy5uFjmPnKAOFGOO/FHs5dg549xHuWvNNvftTh1RiEBlx9OO9VrjA0S1h3LmRlyBIfXuKiS9VNNltCjlpSSWBGKWW+a4FoscUm2AgjJzkgVXI0LmUtjQLxtqckZKFRCOsrY6moImRtTum+T5VAHLHt271AJtR+2yXSRO7lQpx6VVi1B7e+kmlVwXPzpuIP51KhfYcm1uWrRz/AGbOz7MAnblWz/hUyR20dhFHdSJHu5+aNgSfTPeqs2pyTo8YiYRtjAMhOKiuLp7lkLx7RH0XcT/OrVOT8iHOKNN41Oo2wCjAUniFv5d6W2RX1qZ9udi44iJA/wCA9qo/2jJ56zeQu5VKgeY3+NJDeyRSySLEjNL1yx+Wl7KVhc8Rlw26eRvUntj9K2opZFfTFWV1UrgqG4P1FYJ5J5zVuPUrxIkRGhUIMK3lgsB9a1qQbtYiM7SbL07bNPuPLYpm45KnH8quu2J7hwSH+yjkHmsKC9uLcMsflurHLLKm4E+tKt9drcG4EoMrcHKgqR6Y9Kh0pFe0Rr2jlodJZmLHe2CTk4+tNlYGyP2J5Zz9pywm4O70HtWW2o3jzpO0ib4hiMKgCJ+FMW9uUjMayAKX3n5Rnd60vYyH7RGle/6RbXUtvJJBKpH2mFsMCfrRWbdale3MZillQRnlgiBd31PeitacHFWYpTiyMY9KcMcVGDTga1MSUYzTgRTYkeaQJGMsat3dqlnCPMfMhqJTUdGXGEpaor5pc1FnBALdelKSQeRTjNMJQlHVj80hNMzRmqIEbmo2qQmmNQIhija5vo7ZOAeXPtXYwWkMUagIuAOOK5LT5xBqTuUJIXjHeugtNZ867Fs9uFJ6ENmuSrds9LCKMY3NEN5RJQAfhXNeJrNJoTcphZI+uO4q9qOrTw3PkoioO7sM1Tu2lu7GXdgnGQQOtRG6aZvUtJNHOafcF3eMngcir4rPs7eWG6ZpIyquvB7Gr4rtWqPImrSHUtJS0yRacPuimZpVb5RQA7NAyTgda1NMs7bUYzG+Y5V/jU9RTtOsFi8QRwTuCiHcD/e9KnnV7Fcrtcyc0AFjhRk1qeJbOGz1Qi2AEUi7wo7GodEkhjunefG0IetJy924ONnZmcaKt6fbrqWsrbxHEbMTn2oqXUQcjexWBpQaYKkgTzZkQfxGtG7K4JXdjoNHgEFobhlzLJwgqtcaTcXc5lHz7eck8VJeXhju4rWA/dAGBW0sggtVyDlhk1wOT5rnqU6a5bHNNaSTyMs8SgLwNvUH1FVZbeW3XLksue/Wtp3YzFgTgjrWbqYleE7VYkcqfWnGTuOVJWKe6lzVeGUSRhlPB6VJmu2LurnlzjyysPzSGkzSZqiC5pcaSXRWQZBXFaeyzs5lUFVY85x0rGs5Nk45xmluYzcXknnzMIhghE+8a5ai949LDSSgbCSwO5LfNhsbh2qW7EfklUAyRWJ9jtWt5GheaIY+8eMmtFXAtI2Jydg59ayaOnmM3UNyW8EbkZAzn2qgKfcsxuJNxbg9D2qMGu2CtE8qvLmmxwpabmjNUYkiqzZ2ozYGTtGcU1SCBjn6VJbztbzJPE7LIhzxxV/XdRsj9nvbOEhphieNF6N6/jUTk4jSuU7e4mtZRJESrD2oluZZJ/PLEP6jjFQjUopDiEXEBPZ+R+dJK7AnDxuf9lgaz9prqg2J7i5e5KNIclV25rR0WytLuOU3aFwDjhsEVhJNltrAj37Vcs9QawcyxOpbsrDK/WnNrlshxet2aF7atoN/DfWDM0QPy7+3saKqa7r41C0t7SPiQndJIfug+worGPNbUufLe6KYNW9MOLxW7LzVEVoaUpad8dkJrpqP3WFJXmh+iKbrxbJLKcoF+UGul1DQjPKskc8h5ztLYAFczoTpb+IVMrcy/Kors7yZ1i2Bto9fSuNvU9SMboqT6akem+S7Egnls1Sh0eG0jLhmf5TjJzT5rsbfLF68kajkKn9aSOYNAWj3eXjPzcUM0st2chbRmNZFIxtkOBUwNK7bjI2MbnJAqJ5FQZcgCuunseTX+IkzS5rPl1DGREv4mqE1zNJ96Q49BV3M1Bs3VkQyqgcbyeADyaRrsbvtADZxt4HQj1rG0gsdXttvZwa6zWrSG3H2tJkiaVsGMjIY+tZzV9Tan7uhmjUTcRMoVskYFa1rDILI3V2fLtLdcsx43kdAK0dD0NJbZbqd1dSflRVx+dYfjjUmkuF0yEhYYeXC9CfShU1uzV1Gc7LqE0t3LcHjzGzt9BU0V/G3DgqazyKABVmDimbAmjbo4p/XnNYtPWeSP7rGnchwNfufpSocSDeMoRyKzodTAYCQYzxkVe3ADcSMVnUehFmiw+xIMRxnL9gOBUS6TNJEZRbuFPVgvFVpbssrM6MVAwvOKgW+vJZEgSd44gR8gY81jysaXc0HMUS7TguB9wdBWdcFm3PIwQdgKtPajduOSc560/7IhZXmUEZyAeaSaQtCpbICQx27sYHPQUVUlnJ1XKYRQ2MgUVUkxtNGyK6XQbHZayTy/KzLwD6VkaNEtxdrCtvJNKTwEGcfX0ruzoc6xggqSRgr6U6zk1aKOrDxinzSZzi6bDFfQXROTERkjtXQSshHzcirNro2F2uML3qLVNNktl8y2JMXcH+GsFCbV2d0akE+Up3KW5ALPj0AFZN/dIqmGPqw/SrTwyNy549BWdLayXN46xKcouMUblSloZl9EY9pX7uOtYN/LuuQueFFdedLu7lWhQEMozhuAfxrhruQfapCD0JFddN+6ebVjaY1nwai3FzSZyaCccirJOs8G6eJvOu2UMIzhc+tWr9ZJ9UEyup2qUhTGRnvmprAnS/CsZQ7ZLkZzRpMvltAY4vOwCAT0z3JrCtO2iNqULu5u6Oy6bYSPulkG1nmaTsw9PbivNb25a8vJbh+sjFq7fxZd/ZNAjtlbDznBHt1Jrga3TuiGtRGz0FKAcUhPT1NLnigkQ8GopGwMUO1RFsn6UCEVTLcpGP4mArqPssUcWZckqPlXNcvbzCC4EpGSvIHvWvYXL3CO0jEsDUyjzMiQtzE8mWZgo7DsKr2oIvrcu4Khwc1PdOXIjHTPJpsghilyMYTG0nrUPyF0N++8pnzGoB7msy7uBbQFnOSeFHrWuYoRBG4PBG4k/SuV1GU3d0zL/q0GB6VjCN2JK4qTRXMgLoqN6etFUl4wT1HSitnEbTPoLStIstIt/Js4QufvOeWY+5q/RSGtWaCMwUZNRiZHcxkEHHRh1qUgEc0xo1YYbkUm2NWM290sNmS2A90/wAKx7SNrbWF2xtuZDuUiumEGx8xOVB6jqKVoWJ3b8t9BWTgm7o6I12lyvUroI152AFvvV43450oaV4nnSNcQTgSx+nPUfnXtEkZCEmuV8e6F/auki6hUm6tFLJj+Je4qoOzsZzV1dHkg706Nd8ir6kCkXkVb02MPqMAboHyfwrQzOt1RkaS1s1JIt4QNo6E471JAkiwxEkQZ4BXsv196qwmSe7dwoCvkknrippPLSybc3mFTufJ+6PSuOT5pHVBcsTJ8U3v2vVRGpzHAgQfXqaxqc7mWRpW5ZyWP401s9PWus5ho65pGJpxAAprcjimIqu/zYpoPBJofhqRulAhrVoaQzbpFAOCOtZpqzaXBiIXsWBpsUlobgVFGSuaz5kkc4UhRnOccmtEFSORnNPWfygfLjjye7LnFZtO+hCbKd3eTx6bHBkgsMMT6UmlRxXFvIjDJU81BcrJMGlkYszHj6VFavNbS7422k8dM1PLpoPl0sie8sxbpvzxniinhZ52zcHIxxRQtNx6n0BSUHpUZfAq5MtK5JmjIpgbJGKcF5yaE2FhaKMCg09UICoYYNQTRjYRjI9KnBpGGRS3Q07Hh/izRzo+uzRKuIJf3kR9j2/A1mWknky+Z6A16B8UYh9htJtvzJIVz7EV50PmUDOMkU73Q2rM63TEVkhkuD8xHCjuara9dGKA2axbN7Es3rWnpsZCW3kRZ4wCe3vXO+IJQ+qyKpyI/lBrlpK8zpm7QM6kBySaax6AdTSM20V1nKKxzTCabv5pGOaAIJeGzQvIpZRleOtNTkUxAwpnenlTTTxQM3bR/MtkPfGK1hpbNFv85RxnGKwdMb/R8ejV0qyt5B/3axqyatYKUItu5z8G4kqTkKKYseZAKltur/SpYI/nBxVFWEYtGeOTjFFTzx85opIHE9ulkVBgnrVdnz0p910FVixCkjk4pSeppCOlyeB1BJZgBnuasCVD0OfpWXaygSHdDz+YrQWdPYU4ysTOOpKHyehpTUZmWo3nAzzTciFFskZj24p+4Bcms97xFJCDe9KEuJgN8pXPZe1SmXyHIfE6dP7GRMjLTDb+tebQDMkY/wBoVveP9RW51n7FEzslr8rM38T965+2b94h/wBqr+yS3qdwkvlWqvNJtKqWwvT2FcXLKZZXlY/eJatO/vc2QggJVTwc96xZDkhR+NRRjZF1ZX0FU7iWP4UMaOgxTSeK1MhCKZT6Y1ABimREbiDTskGmquU3DqDTAkYVE4qVTuFMccUAX9Fw77D/AHhXXSwEKwUdq5Tw/GTeIT0ZwK78qMntmuTET5ZI2w8U7nHw2siyPGylWx3FWYoQuBXSooPBCsoHQimfZ7Vj81uuT6HFQsQnujX2LTMXyQyyE9qK2jZ2m04hwR/tdaKf1iI/ZSO+mJKDIORVOVXkiZFIQnueasyPIy4kg59QeKrK6o7AAZ9z1rdoyjtYriBhwbkA+wpRE6fcmaQ+iqatrJkjZHk1N5Usg+d9o9F4o0Buxm+ZcBsNG2fQcmnJFcTP86SIv+7mtOOBI/uAfWlLADg0WQuZvYhjgWNcbmX24qznKAg9KhO3rj8zToyBGwyPpQJo8U8dWX2HxXeKDlZSJRznrzWEj7cH05rtfilCi6tazpHtMkRDNn72DXDZ71ruZPRl+7lBEZBySMmq6epqLOQMmnBhQlYG7khNNNN3UE0ALSUmKXFACHFJD/q6U9KSE4j/ABoAVflJqORsnFOY1E3WmBveGxuuoAf71d43Q44+tcP4WGb2EfWu7JyuCBXn4t++jqwi0ZABjpQBzUgA9aaAM1ynZYRlJDNjgUU5iQCO3eincDsf3s4JM3HpjAqncxZUhJvmH8QqzK0h4eUMD/CqmoZRuUpsK5HpivVZ56KqEpgNcmrKywLy0ksh9AKphscbRV2N32jC4HqRgUgJUuSwIjiKr70ByTyDTDPiJv3/AH6KKgaQkcu5/GgEy0ZAOx/E0wSgRyOXGBycc1SIUnGCfxqfdtsHRAPmyM0rDueZ/ES/ku9Ugi48mJP3Z7nPXNcga6bxxGEvoGAwShz+dcxmtlsYPcupcAWBt9g+Y5JxzVbHNCN8tFJIQUoNFFMBd1KWFNpCcGkMGPFNQ/JSOaB90UwFzmmNS0hoEdL4SXN6h9FNduSCB2rjvCC/6WfZK7A9a83F/GduEXusTpk0mOlOZeMUhxkVzHWDdqKGHy0UxHV+VMD8t0OfY/4U7y32kecjZ65BH9KKK9jlR5fOysbKTJ2yxAZ9/wDCnGzLfflRz/tFsfliiinyoXMxWtSIgEMO73zj+VQnT5WOWuU/AED+VFFHKg5mO+wMMKsybe55yf0p0lpIYFSOSIHd82c4x+VFFLlQczOQ8TeCb/V7mOW2u7NAoIPmFx/JTWL/AMKy1f8A5/tO/wC+pP8A4iiiqJHL8NNXA/4/tP8A++pP/iKX/hWurf8AP9p//fUn/wARRRQAv/CtdW/5/tP/AO+pP/iKP+Fa6t/z/af/AN9Sf/EUUUAH/CtdW/5/tP8A++pP/iKafhpq5/5ftP8A++pP/iKKKAGn4Zauf+X7Tv8AvqT/AOIpf+FZ6v8A8/2nf99Sf/EUUUAH/Cs9X/5/tO/76k/+IpP+FZavn/j+07/vqT/4iiigDd0PwXf6dM7zXdmwIwNhf/4mt0aNMP8AltBn6t/hRRWM6EJu7NIVZQVkIdEnPSeD82/wpp0Ocn/Xwfm3+FFFT9Vpl/WagraJORxPB+bf4UUUUfVqYfWZn//Z'],
//    ['name'=>'Remote File','source_file'=>'http://dev.top4.com.au/images/top4/profile_banner_default.jpg'],
//    ['name'=>'Wrong Remote File','source_file'=>'http://dev.top4.com.au/images/top4/profile_banner_default_wrong.jpg'],
//    ['name'=>'Local File','source_file'=>PATH_IMAGE.'/bg_top4_api_login.jpg'],
//    ['name'=>'Wrong Local File','source_file'=>PATH_IMAGE.'/xxs/202_photo_19233.jpg']
//];
//foreach($value as $image_id=>$image_row)
//{
//    $start_time = microtime(1);
//    print_r('<br><br><strong>'.$image_row['name'].'</strong><br>Header:');
//    $file_header = @get_headers($image_row['source_file'],true);
//    print_r($file_header);
//    echo '<br>Time spent: '.(microtime(1)-$start_time).'<br>';
//    $start_time = microtime(1);
//    echo '<br>mime_content_type:';
//    print_r(mime_content_type($image_row['source_file']));
//    echo '<br>Time spent: '.(microtime(1)-$start_time).'<br>';
//    $start_time = microtime(1);
//    echo '<br>getimagesize:';
//    $result = getimagesize($image_row['source_file']);
//    if ($result === false) echo 'false';
//    else print_r($result);
//    echo '<br>Time spent: '.(microtime(1)-$start_time).'<br>';
//    //print_r(file_get_contents($image_row['source_file']));
//}
// Result:
//1. get_header only works for image uri (remote image files) while renders empty results for data uri, local file path and all non-existing files
//2. mime_content_type works for data uri and local file path, it also returns correct mime for broken data uri (if mime info is provided correctly in data uri). However, it raise PHP warnings for all the other cases.
//3. getimagesize works for all formats. If file does not exist, it returns false. However, it also raise PHP warning for non-existing local file path. Solution: use @getimagesize to disable warning on this function call

$entity = new entity_image();
//$row = $entity->get(['where'=>'`data`=""','limit'=>10]);
$row = $entity->get(['where'=>'`id` IN (12968,147620,149732)','limit'=>10]);
print_r($row);
$entity->set(['row'=>$row]);



//$entity = new entity_image();
//$entity->sync();

//$value = [['name'=>'CHIARO Bathroom Package','source_file'=>'http://www.top4.com.au/custom/domain_1/image_files/466_photo_287895.jpg']];
//$parameter = [
//    'fields' => ['name', 'width', 'height', 'mime','data'],
//    'row' => $value
//];
//$entity->set($parameter);
//print_r($entity);
//$entity->get();
//foreach($entity->row as $record_index=>$record)
//{
//    file_put_contents('test_'.$record_index.'.jpg', $record['data']);
//}
//$entity = new entity_image('construction-services-4');
//print_r($entity);

print_r(message::get_instance()->display());