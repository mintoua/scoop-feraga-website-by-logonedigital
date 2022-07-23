/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package edu.entities;

/**
 *
 * @author hbaie
 */
public class Guide {

    private int id ;  
    
    private String name ;
    private String email ; 
    private int nbr_expr ; 
    private String description ; 
    
    public Guide() {
    }
    
    public Guide(String name, String email, int nbr_expr, String description) {
        this.setName(name);
        this.setEmail(email);
        this.setNbr_expr(nbr_expr);
        this.setDescription(description);
    }
    
    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public int getNbr_expr() {
        return nbr_expr;
    }

    public void setNbr_expr(int nbr_expr) {
        this.nbr_expr = nbr_expr;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public int getId() {
        return id;
    }
    public void setId(int id) {
        this.id = id;
    }

    

    


    
}
