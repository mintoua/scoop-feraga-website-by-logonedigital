/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package Services;

import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

import Util.Myconnection;
import edu.entities.Offre;
import edu.entities.Resoffre;

/**
 *
 * @author hbaie
 */
public class ServicesResoffre {

    public boolean verif_offre(Resoffre resoff)
    {
        boolean test = true ; 
        Offre off = new Offre(); 
        ServicesOffre serviceoff = new ServicesOffre() ; 
        off = serviceoff.Search(resoff.getId_offre()) ; 
        if (off!=null) {
            System.out.println("eyy baba") ; 
            System.out.println("date valable de  : " + off.getValablede() + "date res : " + resoff.getDate_res()) ; 
            if (resoff.getDate_res().after(off.getValablede()) && resoff.getDate_res().before(off.getValablea()) ) {
                System.out.println("date valide ") ; 

            }
            else {
                System.out.println("date non valide ") ; 
                test = false ;
            }

        }
        else{
            System.out.println("offre not found ") ; 
            test = false ; 
        }

        return test ; 

    }




    public void CreateResoffre(Resoffre resoff) {
        
        if (verif_offre(resoff)) {
            
            try {

                String query = " insert into Resoffre (nbr_de_place, date_res, user_id , offre_id)"
                        + " values (?, ?, ?, ?)";
                // drop matekhdemch fl prepared , prepred statment drequette dynamique
                Myconnection conn = new Myconnection();
                conn.connect();
    
                PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
                preparedStmt.setInt(1, resoff.getNbr_place());
                preparedStmt.setDate(2, (Date) resoff.getDate_res() );
                preparedStmt.setInt(3, resoff.getId_user() );
                preparedStmt.setInt(4, resoff.getId_offre() );
                preparedStmt.execute();
                System.out.println("resoff created ") ; 
                // System.out.println("guide added " + preparedStmt.executeUpdate(query));
            } catch (SQLException ex) {
                System.out.println(ex.getMessage());
            }
        }

    }

    public List<Resoffre> ViewResoffre() {
        List<Resoffre> myList = new ArrayList<>();
        String query = "SELECT * FROM Resoffre";
        Statement st;
        Myconnection conn = new Myconnection();
        conn.connect();
        try {
            st = conn.getConn().createStatement();
            ResultSet rs = st.executeQuery(query);

            while (rs.next()) {
                Resoffre resoff = new Resoffre();
                
                resoff.setId(rs.getInt(1));
                resoff.setId_user(rs.getInt("user_id"));
                resoff.setId_offre(rs.getInt("offre_id"));
                resoff.setDate_res(rs.getDate("date_res"));
                resoff.setNbr_place(rs.getInt("nbr_de_place"));
                
                

                myList.add(resoff);
                //System.out.println(g.getName() );
            }
        } catch (SQLException ex) {
        }
        return myList;
    }


    public void UpdateResoffre(Resoffre resoff) {
        try {

            String query = " update Resoffre set nbr_de_place = ? , date_res = ? , user_id = ? , offre_id = ? where id = ?; " ;
            // drop matekhdemch fl prepared , prepred statment drequette dynamique
            Myconnection conn = new Myconnection();
            conn.connect();

            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
            preparedStmt.setInt(1, resoff.getNbr_place());
            preparedStmt.setDate(2, (Date) resoff.getDate_res() );
            preparedStmt.setInt(3, resoff.getId_user() );
            preparedStmt.setInt(4, resoff.getId_offre() );
            preparedStmt.setInt(5, resoff.getId() );
            preparedStmt.execute();
            System.out.println("resoffre updated ");
            // System.out.println("guide added " + preparedStmt.executeUpdate(query));
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }

    }

    public void DeleteResoffre( Resoffre resoff) {
        try {

            String query = " delete from Resoffre where id = ? ; " ;
            // drop matekhdemch fl prepared , prepred statment drequette dynamique
            Myconnection conn = new Myconnection();
            conn.connect();

            PreparedStatement preparedStmt = conn.getConn().prepareStatement(query);
        
            preparedStmt.setInt(1, resoff.getId() );
            preparedStmt.execute();
            System.out.println("resoffre deleted ");
            // System.out.println("guide added " + preparedStmt.executeUpdate(query));
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }

    }

    public void trendingoffre()
    {
        ServicesResoffre resoffreserv = new ServicesResoffre();
        List<Resoffre> MyResoffreList = new ArrayList<>(resoffreserv.ViewResoffre() ) ;
        Map<Integer, Integer> count = MyResoffreList.stream().collect(Collectors.groupingBy(e -> e.getId_offre(),Collectors.summingInt(s ->s.getNbr_place()) ) );

        System.out.println(count) ; 
        System.out.println(count.size()) ;
        int max = 0 ;
        int id_offre_max = 0 ;  
        for (Map.Entry<Integer, Integer> entry : count.entrySet() ) {
            
            System.out.println("Key : " + entry.getKey() + " Value : " + entry.getValue());
            if (entry.getValue()>max ) {
                max = entry.getValue();
                id_offre_max = entry.getKey();
            }
        }
        System.out.println("trending offer with the most reservations : " + id_offre_max) ; 

    }



    
}
