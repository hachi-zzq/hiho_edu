<?php namespace HiHo\Subtitle;

/**
 * User: luyu
 * Date: 13-11-14
 * Time: 上午9:40
 */

class Translate
{

    /**
     * 返回新XML
     * @author guanjun hualong
     * @param $name
     * @param $doc
     * @return string
     */
    function exportXML($name, $doc)
    {
        header("Content-type:text/html;charset=utf-8");
        $doc = $this->prepare_xml($doc);
        ##创建新的xml
        $domDocument = new DOMDocument();
        $domDocument->formatOutput = true;

        $domDocument->load($name);

        $domElement = $domDocument->createElement('naphoo');
        $domAttribute = $domDocument->createAttribute('ver');
        $domAttribute->value = '1.1';
        $d = $domDocument->createElement('d');
        $url = $domDocument->createElement('url');
        $date = $domDocument->createElement('date');
        $title = $domDocument->createElement('title', 'HiHo Subtitle');
        $score = $domDocument->createElement('score', '0.290');
        $d->appendChild($url);
        $d->appendChild($title);
        $d->appendChild($date);
        $d->appendChild($score);

        $out = array();
        foreach ($doc as $i => $line) {

            $no = $i + 1;
            $st = $line->st;
            $et = $line->et;
            $text = $line->text;
            //传递汉字到youdao Api
            $trans = $this->getYoudao("http://fanyi.youdao.com/openapi.do?keyfrom=AutoTiming&key=1629116153&type=data&doctype=json&version=1.1&q=" . urlencode($text), urldecode($text));
            $trans = json_decode($trans);
            $word_count = str_word_count($trans->translation['0']); //统计单词个数

            $p = $domDocument->createElement('p');
            $d->appendChild($p);
            $cst = $domDocument->createElement('st', $st);
            $score_t = $domDocument->createElement('score', '1.0214');
            $u = $domDocument->createElement('u');
            $p->appendChild($cst);
            $p->appendChild($score_t);
            $p->appendChild($u);
            $st_2 = $domDocument->createElement('st', $st);
            $score_3 = $domDocument->createElement('score', '0.180');
            $gender = $domDocument->createElement('gender', '1');
            $u->appendChild($st_2);
            $u->appendChild($score_3);
            $u->appendChild($gender);

            $strings = explode(' ', $trans->translation['0']);
            $new_st = round($line->et / $word_count, 3);
            foreach ($strings as $k => $v) {
                $w = $domDocument->createElement('w');
                $u->appendChild($w);
                $st_3 = $domDocument->createElement('st', $st + $new_st * $k);
                $cet = $domDocument->createElement('et', $et);
                $score_4 = $domDocument->createElement('score', '1.22');
                $token = $domDocument->createElement('token', $v);
                $w->appendChild($st_3);
                $w->appendChild($cet);
                $w->appendChild($score_4);
                $w->appendChild($token);
            }

        }

        $domElement->appendChild($d);
        $domElement->appendChild($domAttribute);
        $domDocument->appendChild($domElement);
        return $domDocument->saveXML();

    }

    /**
     * 解析XML
     * @author guanjun hualong
     * @param $subtitle
     * @return array
     */
    function prepare_xml($subtitle)
    {
        $xml = simplexml_load_string($subtitle);
        $doc = array();

        $lines = $xml->xpath('//p');

        foreach ($lines as $line) {
            $st = (float)$line->st;
            $et = (float)$line->xpath('(.//et)[last()]')[0];
            $words = $line->xpath('u/w/token');
            $pre_word = null;
            $text = '';
            foreach ($words as $word) {
                $w = (string)$word;
                if ($pre_word !== null and preg_match("/^(\w|\d)/", $w)) {
                    if (!preg_match("/^(ve|s|ll)$/i", $w) or !preg_match("/^('|’)$/", $pre_word))
                        $text .= ' ';
                }
                $text .= $w;
                $pre_word = $w;
            }

            $doc[] = (object)array(
                'st' => $st,
                'et' => $et,
                'text' => $text);
        }

        return $doc;
    }

} 