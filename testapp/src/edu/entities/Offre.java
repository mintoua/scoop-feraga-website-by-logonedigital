/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package edu.entities;

import java.sql.Date;

/**
 *
 * @author hbaie
 */
public class Offre implements Comparable<Offre>{

    private int id;
    private String  name;
    private String  destination;
    private int vol;
    private int hotel;
    private int Guide;
    private int place_dispo;
    private int price;
    private Date valablede;
    private Date valablea;
    private String image;
    private String description;

    public Offre(String name, String destination, int vol, int hotel, int guide, int place_dispo, int price,
            Date valablede, Date valablea, String image, String description) {
        this.setName(name);
        this.setDestination(destination);
        this.setVol(vol);
        this.setHotel(hotel);
        setGuide(guide);
        this.setPlace_dispo(place_dispo);
        this.setPrice(price);
        this.setValablede(valablede);
        this.setValablea(valablea);
        this.setImage(image);
        this.setDescription(description);
    }
    @Override
    public String toString() {
        return "Offre{" + "name=" + name + ", destination=" + destination + ", vol=" + vol + ", hotel=" + hotel + ", Guide=" + Guide + ", place_dispo=" + place_dispo + ", price=" + price + ", valablede=" + valablede + ", valablea=" + valablea + ", image=" + image + ", description=" + description + '}';
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getDestination() {
        return destination;
    }

    public void setDestination(String destination) {
        this.destination = destination;
    }

    public int getVol() {
        return vol;
    }

    public void setVol(int vol) {
        this.vol = vol;
    }

    public int getHotel() {
        return hotel;
    }

    public void setHotel(int hotel) {
        this.hotel = hotel;
    }

    public int getGuide() {
        return Guide;
    }

    public void setGuide(int guide) {
        this.Guide = guide;
    }

    public int getPlace_dispo() {
        return place_dispo;
    }

    public void setPlace_dispo(int place_dispo) {
        this.place_dispo = place_dispo;
    }

    public int getPrice() {
        return price;
    }

    public void setPrice(int price) {
        this.price = price;
    }

    public Date getValablede() {
        return valablede;
    }

    public void setValablede(Date valablede) {
        this.valablede = valablede;
    }

    public Date getValablea() {
        return valablea;
    }

    public void setValablea(Date valablea) {
        this.valablea = valablea;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public Offre() {
    }

    @Override
    public int compareTo(Offre off) {
        return getPlace_dispo() - off.getPlace_dispo();
    }


    
}










