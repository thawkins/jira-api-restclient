<?php
/*
 * The MIT License
 *
 * Copyright (c) 2014 Shuhei Tanuma
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace thawkins\Jira\Users;


class Entity
{
    /**
     * Creates user instance.
     *
     * @param array $user User.
     */
    public function __construct(array $user = array())
    {
       foreach($user as $key=>$value){
           $this->$key = $value;
           if($key === 'displayName'){
               $pos = stripos($value, '@redflaggroup.com');
               if($pos !==false){
                   $this->email=$value;
                   $name = substr($value,0,$pos);
                   $name = ucwords(strtr($name, ".", " "));
                   $this->$key = $name;
                   $this->mode = "email";
               } else {
                   $pos = strpos($value, " ");
                   if ($pos !=false) {
                       $res = array();
                       $tok = strtok($value, " ,.\n\t");
                       $val = trim(strtolower($tok));
                       if (!empty($val)) {
                           $res[] = $val;
                       }
                       while ($tok !== false) {
                           $tok = strtok(" .,\n\t");
                           $val = trim(strtolower($tok));
                           if (!empty($val)) {
                               $res[] = $val;
                           }
                       }
                       $address = "unknown@example.com";
                       $lastName = trim(array_pop($res));
                       if (count($res) >= 1) {
                           $address = (strtolower($res[0]) . "." . strtolower($lastName));
                           $address .= '@redflaggroup.com';
                       }
                       $this->email = $address;
                       $this->mode = "name+space";
                   } else {
                       $res = array();
                       $tok = strtok($value, " ,.\n\t");
                       $val = trim(strtolower($tok));
                       if (!empty($val)) {
                           $res[] = $val;
                       }
                       while ($tok !== false) {
                           $tok = strtok(" .,\n\t");
                           $val = trim(strtolower($tok));
                           if (!empty($val)) {
                               $res[] = $val;
                           }
                       }
                       $lastName = trim(array_pop($res));
                       if (count($res) >= 1) {
                           $name = (ucfirst($res[0]) . " " . ucfirst($lastName));
                           $this->$key = $name;
                           $address = (trim(strtolower($res[0]) . "." . strtolower($lastName)) . '@redflaggroup.com');
                           $this->email = $address;
                       } else {
                           $this->email = 'unknown@example.com';
                       }
                       $this->mode = "name+dots";
                   }
               }
           }


       }
    }


    /**
     * Gets jira's internal user id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->accountId;
    }
}
