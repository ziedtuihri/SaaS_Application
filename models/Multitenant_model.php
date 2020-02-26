<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 */
class Multitenant_model extends CI_Model
{
    public function read()
    {
        // get all the members table
        $query = $this->db->query(" SELECT * FROM `prefix_members` ");
        return $query->result_array();

    }

    public function getTenant($email)
    {

        $username = "";
        $data = 0;
        $Id_tenant = array();
        $description = array();
        // get members of instance have an email
        $result = $this->db->query(" SELECT email, id_tenant, description FROM `prefix_members` WHERE `email` ='" . $email . "'; ");

        if ($result->num_rows() == 1) {
            $data = 1;
            $result = $result->result_array();
            foreach ($result as $columnInfo) {
                $Id = $columnInfo['id_tenant'];
                array_push($Id_tenant, $Id);
                $descriptionB = $columnInfo['description'];
                array_push($description, $descriptionB);
            }
            $result_suspend = $this->db->query(" SELECT * FROM `prefix_suspend` WHERE `id_tenant` ='" . $Id . "' and etat = 'false'; ");
            if ($result_suspend->num_rows() == 1) {
                $data = 0;
               //  unset($Id_tenant);
            }
        } else if ($result->num_rows() > 1) {
            $result = $result->result_array();
            foreach ($result as $columnInfo) {
                $Id = $columnInfo['id_tenant'];
                $descriptionB = $columnInfo['description'];
                $result_suspend = $this->db->query(" SELECT * FROM `prefix_suspend` WHERE `id_tenant` ='" . $Id . "' and etat = 'false'; ");
                if ($result_suspend->num_rows() != 1) {
                    array_push($Id_tenant, $Id);
                    array_push($description, $descriptionB);
                    $data++;
                }

            }
        }

        $tab[0] = $data;
        $tab[1] = $Id_tenant;
        $tab[2] = $description;
        return $tab;
    }


}
