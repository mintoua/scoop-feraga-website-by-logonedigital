/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package Services;

import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

import Util.Myconnection;
import edu.entities.Offre;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

/**
 *
 * @author hbaie
 */
public class ServicesOffre {

    public boolean verif_vol_guide_hotel(Offre off) {
        boolean vol = true ; 
        boolean hotel = true ; 
        boolean guide = true ; 
        boolean found_all = true ; 
        try {
            Myconnection conn = new Myconnection();
            conn.connect();

            String query_vol = " SELECT *  from Vol where id = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query_vol);
            preparedStmt.setInt(1, off.getVol() );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("vol not found "); 
                vol = false  ; 
            }

            String query_hotel = " SELECT *  from Hotel where id = ? ; ";
            PreparedStatement st_hotel = conn.getConn().prepareStatement(query_hotel);
            st_hotel.setInt(1, off.getHotel() );
            ResultSet rs_hotel = st_hotel.executeQuery();
            if (rs_hotel.next() == false)
            {
                System.out.println("hotel not found "); 
                hotel = false  ; 
            }

            String query_guide = " SELECT *  from Guide where id = ? ; ";
            PreparedStatement st_guide = conn.getConn().prepareStatement(query_guide);
            st_guide.setInt(1, off.getGuide() );
            ResultSet rs_guide = st_guide.executeQuery();
            if (rs_guide.next() == false)
            {
                System.out.println("Guide not found "); 
                guide = false  ; 
            }

        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }
        
        if (guide==false || hotel==false || vol==false )  {
            found_all = false ;           
        }

        return found_all ; 


    }


    public void CreateOffre(Offre off) {

        if (off.getValablea().after(off.getValablede()) == true && !off.getName().isEmpty()
        && !off.getDestination().isEmpty() && verif_vol_guide_hotel(off)==true && 
        off.getValablea().after(off.getValablede()) && !off.getDestination().isEmpty() 
        && !off.getDescription().isEmpty() && off.getPrice()>0 && off.getPlace_dispo()>0 )
        {
            try {
                String query = " insert into Offre (name, destination, place_dispo , price , valablede , valablea , image , description , vol_id , hotel_id , guide_id )"
                        + " values ( ?, ? , ? , ?, ?, ? , ? , ? ,? , ? ,?  )";
                // drop matekhdemch fl prepared , prepred statment drequette dynamique
                Myconnection conn = new Myconnection();
                conn.connect();
                PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
                preparedStmt.setString(1, off.getName());
                preparedStmt.setString(2, off.getDestination());
                preparedStmt.setInt(3, off.getPlace_dispo());
                preparedStmt.setInt(4, off.getPrice());
                preparedStmt.setDate(5, off.getValablede());
                preparedStmt.setDate(6, off.getValablea());
                preparedStmt.setString(7, off.getImage());
                preparedStmt.setString(8, off.getDescription());
                preparedStmt.setInt(9, off.getVol());
                preparedStmt.setInt(10, off.getHotel());
                preparedStmt.setInt(11, off.getGuide());

                preparedStmt.execute();

                System.out.println("offre added ");
            } catch (SQLException ex) {
                System.out.println(ex.getMessage());
            }

        }
        else{
            System.out.println("error check your attributes  ");

        }

    }

    public List<Offre> Viewoffre() {
        List<Offre> myList = new ArrayList<>();
        String query = "SELECT * FROM Offre";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
                Offre off = new Offre();
                off.setId(rs.getInt(1));
                off.setName(rs.getString("name"));
                off.setDestination(rs.getString("destination"));
                off.setPlace_dispo(rs.getInt("place_dispo"));
                off.setPrice(rs.getInt("price"));
                off.setValablea(rs.getDate("valablea"));
                off.setValablede(rs.getDate("valablede"));
                off.setImage(rs.getString("image"));
                off.setDescription(rs.getString("description"));
                off.setVol(rs.getInt("vol_id"));
                off.setHotel(rs.getInt("hotel_id"));
                off.setGuide(rs.getInt("guide_id"));

                myList.add(off);
                // System.out.println(g.getName() );
            }
        } catch (SQLException ex) {
        }
        return myList;
    }

    public void UpdateOffre(Offre off) {
        try {

            String query = " UPDATE Offre set name = ? , destination = ? , place_dispo = ?  , price = ? , valablede = ? , valablea = ? , image = ?, description = ?, vol_id =?, hotel_id = ?, guide_id = ? where id = ?;";
            // drop matekhdemch fl prepared , prepred statment drequette dynamique
            Myconnection conn = new Myconnection();
            conn.connect();

            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setString(1, off.getName());
            preparedStmt.setString(2, off.getDestination());
            preparedStmt.setInt(3, off.getPlace_dispo());
            preparedStmt.setInt(4, off.getPrice());
            preparedStmt.setDate(5, off.getValablede());
            preparedStmt.setDate(6, off.getValablea());
            preparedStmt.setString(7, off.getImage());
            preparedStmt.setString(8, off.getDescription());
            preparedStmt.setInt(9, off.getVol());
            preparedStmt.setInt(10, off.getHotel());
            preparedStmt.setInt(11, off.getGuide());
            preparedStmt.setInt(12, off.getId());

            preparedStmt.execute();

            System.out.println("offre updated ");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }

    }

    public void DeleteOffre(Offre off) {
        try {

            String query = " delete from Offre where id = ? ; ";
            // drop matekhdemch fl prepared , prepred statment drequette dynamique
            Myconnection conn = new Myconnection();
            conn.connect();

            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);

            preparedStmt.setInt(1, off.getId());
            preparedStmt.execute();
            System.out.println("resoffre deleted ");
            // System.out.println("guide added " + preparedStmt.executeUpdate(query));
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }

    }

    public List<Offre> SortOffre(int type) {
        List<Offre> myList = new ArrayList<>();
        String query = "SELECT * FROM Offre";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
                Offre off = new Offre();
                off.setId(rs.getInt(1));
                off.setName(rs.getString("name"));
                off.setDestination(rs.getString("destination"));
                off.setPlace_dispo(rs.getInt("place_dispo"));
                off.setPrice(rs.getInt("price"));
                off.setValablea(rs.getDate("valablea"));
                off.setValablede(rs.getDate("valablede"));
                off.setImage(rs.getString("image"));
                off.setDescription(rs.getString("description"));
                off.setVol(rs.getInt("vol_id"));
                off.setHotel(rs.getInt("hotel_id"));
                off.setGuide(rs.getInt("guide_id"));

                myList.add(off);
                // System.out.println(g.getName() );
            }
        } catch (SQLException ex) {
        }

        Collections.sort(myList);

        if (type == 1) {
            // descending
            Collections.reverse(myList);
        }

        return myList;
    }

    public void stat()
    {
        ServicesOffre serviceoff = new ServicesOffre() ;
        List<Offre> MyoffreList = new ArrayList<>( serviceoff.Viewoffre() ) ; 
        Map<String, Long> count = MyoffreList.stream().collect(Collectors.groupingBy(e -> e.getDestination() ,Collectors.counting() ) );
        System.out.println(count) ;  
        int sum = 0 ; 
        for (Map.Entry<String, Long> entry : count.entrySet() ) {
            
            System.out.println("Key : " + entry.getKey() + " Value : " + entry.getValue());
            sum += entry.getValue(); 
        }
        for (Map.Entry<String, Long> entry : count.entrySet() ) {

            System.out.println(entry.getKey() + " : " + entry.getValue() * 100 / sum +"%" ) ; 

        }


        
        
        

    }


    public Offre Search(int id ) {
        Offre off_final = new Offre(); 
        
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            String query = " SELECT *  from Offre where id = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setInt(1, id );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("offre not found "); 
                
            }
            else {
                
                    System.out.println("offre found   ") ; 
                    off_final.setId(rs.getInt(1));
                    off_final.setName(rs.getString("name"));
                    off_final.setDestination(rs.getString("destination"));
                    off_final.setPlace_dispo(rs.getInt("place_dispo"));
                    off_final.setPrice(rs.getInt("price"));
                    off_final.setValablea(rs.getDate("valablea"));
                    off_final.setValablede(rs.getDate("valablede"));
                    off_final.setImage(rs.getString("image"));
                    off_final.setDescription(rs.getString("description"));
                    off_final.setVol(rs.getInt("vol_id"));
                    off_final.setHotel(rs.getInt("hotel_id"));
                    off_final.setGuide(rs.getInt("guide_id"));                

            }
            
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return off_final;
    }
   
    public ObservableList<Offre> ViewOffrefx() {
        ObservableList<Offre> myList = FXCollections.observableArrayList();
        String query = "SELECT * FROM Offre";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
                Offre off = new Offre();
                off.setId(rs.getInt(1));
                off.setName(rs.getString("name"));
                off.setDestination(rs.getString("destination"));
                off.setPlace_dispo(rs.getInt("place_dispo"));
                off.setPrice(rs.getInt("price"));
                off.setValablea(rs.getDate("valablea"));
                off.setValablede(rs.getDate("valablede"));
                off.setImage(rs.getString("image"));
                off.setDescription(rs.getString("description"));
                off.setVol(rs.getInt("vol_id"));
                off.setHotel(rs.getInt("hotel_id"));
                off.setGuide(rs.getInt("guide_id"));
                myList.add(off);
            }
        } catch (SQLException ex) {
        }
        return myList;
    }

    public ObservableList<Offre> SortOffrefx() {
        ObservableList<Offre> myList = FXCollections.observableArrayList();
        String query = "SELECT * FROM Offre";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
                Offre off = new Offre();
                off.setId(rs.getInt(1));
                off.setName(rs.getString("name"));
                off.setDestination(rs.getString("destination"));
                off.setPlace_dispo(rs.getInt("place_dispo"));
                off.setPrice(rs.getInt("price"));
                off.setValablea(rs.getDate("valablea"));
                off.setValablede(rs.getDate("valablede"));
                off.setImage(rs.getString("image"));
                off.setDescription(rs.getString("description"));
                off.setVol(rs.getInt("vol_id"));
                off.setHotel(rs.getInt("hotel_id"));
                off.setGuide(rs.getInt("guide_id"));

                myList.add(off);
                // System.out.println(g.getName() );
            }
        } catch (SQLException ex) {
        }

        Collections.sort(myList);
        return myList;
    }




    public String GetHotelName(int id ) {
        String name = "asdasdasdasd" ; 
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            String query = " SELECT name from Hotel where id = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setInt(1, id );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("hotel not found "); 
                
            }
            else {
                    name=rs.getString(1);
            }
            
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return name ; 
    }

    public String GetVolName(int id ) {
        String name = "asdasdasdasd" ; 
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            String query = " SELECT name from Vol where id = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setInt(1, id );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("vol not found "); 
                
            }
            else {
                    name=rs.getString(1);
            }
            
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return name ; 
    }

    public String GetGuideName(int id ) {
        Myconnection conn = new Myconnection();
        String name = "asdasdasdasd" ; 

        conn.connect();
        try {
            String query = " SELECT name from Guide where id = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setInt(1, id );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("guide not found "); 
                
            }
            else {
                    
                name=rs.getString(1);
            }
            
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return name ; 
    }


    public ObservableList<String> GetListVol() {
        ObservableList<String> myList = FXCollections.observableArrayList();
        String query = "SELECT name FROM Vol";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
             
                myList.add( rs.getString(1) );
            }
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return myList;
    }

    public ObservableList<String> GetListHotel() {
        ObservableList<String> myList = FXCollections.observableArrayList();
        String query = "SELECT name FROM Hotel";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
             
                myList.add( rs.getString(1) );
            }
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return myList;
    }

    public ObservableList<String> GetListGuide() {
        ObservableList<String> myList = FXCollections.observableArrayList();
        String query = "SELECT name FROM Guide";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
             
                myList.add( rs.getString(1) );
            }
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return myList;
    }



    public int GetHotelId(String name ) {
        Myconnection conn = new Myconnection();
        int id = -1 ; 
        conn.connect();
        try {
            String query = " SELECT id from Hotel where name = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setString(1, name );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("hotel not found "); 
                
            }
            else {
                    id=rs.getInt(1);
            }
            
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }

        return id ; 
    }

    public int GetVolId(String name) {
        Myconnection conn = new Myconnection();
        int id = -1 ; 

        conn.connect();
        try {
            String query = " SELECT id from Vol where name = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setString(1, name );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("vol not found "); 
                
            }
            else {
                id=rs.getInt(1);
            }
            
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return id ; 
    }

    public int GetGuideId(String name ) {
        Myconnection conn = new Myconnection();
        int id = -1 ; 
        conn.connect();
        try {
            String query = " SELECT id from Guide where name = ? ; ";
            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setString(1, name );
            ResultSet rs = preparedStmt.executeQuery();
            if (rs.next() == false)
            {
                System.out.println("guide not found "); 
                
            }
            else {
                    id=rs.getInt(1);
            }
            
        } catch (SQLException ex) {
        }
        finally{
            try {
                conn.getConn().close();
            } catch (SQLException e) {
                e.printStackTrace();
            }

        }
        return id ; 
    }


}
