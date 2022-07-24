/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package edu.entities;

import java.util.Date;

/**
 *
 * @author hbaie
 */
public class Resoffre {
    private int id ; 
    private int nbr_place  ; 
    private int id_user ; 
    private int id_offre ;
    private Date date_res ; 


    public Resoffre() {
    }


    public Resoffre(int nbr_place, int id_user, int id_offre, Date date_res) {
        this.setNbr_place(nbr_place);
        this.setId_user(id_user);
        this.setId_offre(id_offre);
        this.setDate_res(date_res);
    }


    public int getId() {
        return id;
    }


    public void setId(int id) {
        this.id = id;
    }


    public int getNbr_place() {
        return nbr_place;
    }


    public void setNbr_place(int nbr_place) {
        this.nbr_place = nbr_place;
    }


    public int getId_user() {
        return id_user;
    }


    public void setId_user(int id_user) {
        this.id_user = id_user;
    }


    public int getId_offre() {
        return id_offre;
    }


    public void setId_offre(int id_offre) {
        this.id_offre = id_offre;
    }


    public Date getDate_res() {
        return date_res;
    }


    public void setDate_res(Date date_res) {
        this.date_res = date_res;
    }


    
}
