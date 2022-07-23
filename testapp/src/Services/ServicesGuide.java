/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package Services;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import java.util.ArrayList;
import java.util.List;

import Util.Myconnection;

import edu.entities.Guide;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

/**
 *
 * @author hbaie
 */
public class ServicesGuide {

    
    public void CreateGuide2(Guide g) {
        
        
        if ( g.getEmail().indexOf("@gmail.com")!=-1 ) {
            try {

                String query = " insert into Guide (name, email, experience, description)"
                        + " values (?, ?, ?, ?)";
                // drop matekhdemch fl prepared , prepred statment drequette dynamique
                Myconnection conn = new Myconnection();
                conn.connect();
                PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
                preparedStmt.setString(1, g.getName());
                preparedStmt.setString(2, g.getEmail());
                preparedStmt.setInt(3, g.getNbr_expr());
                preparedStmt.setString(4, g.getDescription());
                preparedStmt.execute();
                System.out.println("guide added ") ; 
                // System.out.println("guide added " + preparedStmt.executeUpdate(query));
            } catch (SQLException ex) {
                System.out.println(ex.getMessage());
            }
        }
        else {
            System.out.println("error") ; 
        }

    }

    public List<Guide> ViewGuide() {
        List<Guide> myList = new ArrayList<>();
        String query = "SELECT * FROM Guide";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
                Guide g = new Guide();
                g.setId(rs.getInt(1));
                g.setName(rs.getString("name"));
                g.setEmail(rs.getString("email"));
                g.setNbr_expr(rs.getInt("experience"));
                g.setDescription(rs.getString("description"));
                myList.add(g);
                //System.out.println(g.getName() );
            }
        } catch (SQLException ex) {
        }
        return myList;
    }


    // view fx  

    public ObservableList<Guide> ViewGuidefx() {
        ObservableList<Guide> myList = FXCollections.observableArrayList();
        String query = "SELECT * FROM Guide";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
                Guide g = new Guide();
                g.setId(rs.getInt(1));
                g.setName(rs.getString("name"));
                g.setEmail(rs.getString("email"));
                g.setNbr_expr(rs.getInt("experience"));
                g.setDescription(rs.getString("description"));
                myList.add(g);
                //System.out.println(g.getName() );
            }
        } catch (SQLException ex) {
        }
        return myList;
    }



    public int getguideid(Guide g )
    {
        int id = -1 ; 
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            String query = " SELECT id from Guide where name = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setString(1, g.getName() );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("guide not found "); 
                
            }
            else {

                System.out.println("guide found   ") ; 
                id = rs.getInt(1);
            }
            
        } catch (SQLException ex) {
        }

        return id  ;
    }




    public void UpdateGuide(Guide g ) {
        try {

            String query = " update Guide set name = ? , email = ? , experience = ? , description = ? where id = ?; " ;
            Myconnection conn = new Myconnection();
            conn.connect();

            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setString(1, g.getName());
            preparedStmt.setString(2, g.getEmail());
            preparedStmt.setInt(3, g.getNbr_expr());
            preparedStmt.setString(4, g.getDescription());
            preparedStmt.setInt(5, g.getId());
            preparedStmt.execute();
            System.out.println("guide updated ");
            // System.out.println("guide added " + preparedStmt.executeUpdate(query));
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }

    }


    public void DeleteGuide(Guide g) {
        try {
            
            String query = " delete from Guide where id = ? ; " ;
            // drop matekhdemch fl prepared , prepred statment drequette dynamique
            Myconnection conn = new Myconnection();
            conn.connect();

            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
        
            preparedStmt.setInt(1, g.getId());
            preparedStmt.execute();
            System.out.println("guide deleted ");
            // System.out.println("guide added " + preparedStmt.executeUpdate(query));
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }

    }




}
