/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package Util;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 *
 * @author hbaie
 */
public class Myconnection {

    String jdbcurl = "jdbc:mysql://localhost:3306/testjava";
    String username = "root";
    String password = "";

    Connection conn;


    public void connect() {
        try {
            conn = DriverManager.getConnection(jdbcurl, username, password);

           // System.out.println("Connected to db ");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }
    }

    /*
    public static Myconnection getInstance() {
        if(instance == null){
            instance = new Myconnection();
        }
        return instance;
    }
    */
    
    public Connection getConn() {
        return conn;
    }

    

    

}
