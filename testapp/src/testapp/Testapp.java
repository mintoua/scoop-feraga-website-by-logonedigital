/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Main.java to edit this template
 */
package testapp;


import java.io.FileOutputStream;
import java.sql.Date;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;
import com.itextpdf.text.Document;
import com.itextpdf.text.Paragraph;
import com.itextpdf.text.pdf.PdfWriter;

import Services.ServicesGuide;
import Services.ServicesOffre;
import Services.ServicesResoffre;
import edu.entities.Guide;
import edu.entities.Offre;
import edu.entities.Resoffre;

/**
 *
 * @author hbaie
 */
public class Testapp {

    /**
     * @param args the command line arguments
     * @throws ParseException
     */
    public static void main(String[] args)  {

        

        // Guide g = new Guide("ahmed77", "ahmed.hbaieb@gmail.com", 10, "hellooo");
        Guide g2 = new Guide("ahmed2222", "ahmed.hbaieb@gmail.com", 20, "hellooo");
        Guide g3 = new Guide("ahlsssssa", "ahmed22223333.hbaieb@gmail.com", 20, "hellooo2222");
        ServicesGuide guideserv = new ServicesGuide();
       // guideserv.CreateGuide2(g3);
        //System.out.println(guideserv.ViewGuide() );
        List<Guide> MyGuideList = new ArrayList<>( guideserv.ViewGuide() ) ; 
        g3.setId(2) ; 
        guideserv.UpdateGuide(g3 );
        System.out.println( guideserv.getguideid(g3 ) ) ;  
       // guideserv.DeleteGuide(g3);
        
        /*
        for (Guide g : MyGuideList) {
            System.out.println("name : " + g.getName() + " , " + " email : " 
            + g.getEmail() + " , "+ " nbr_experience : " + g.getNbr_expr() +
            " , " + "description : " + g.getDescription() ) ; 
            
        }
        */

        
        ServicesResoffre resoffreserv = new ServicesResoffre();
        Date firstDate1 = new Date(123, 04, 20);
        Date firstDate2 = new Date(125, 04, 20);
        Resoffre res1 = new Resoffre(100  , 10 , 36 , firstDate1); 

        //System.out.println(resoffreserv.verif_offre(res1)) ;


        //System.out.println("date 1 : " +firstDate1 + " , date 2 : " +  firstDate2);
        //System.out.println( firstDate2.after(firstDate1) ) ; 

       // Resoffre res1 = new Resoffre(100  , 10 , 27 , firstDate1); 
        //res1.setId(22) ;
       // resoffreserv.CreateResoffre(res1);
        //resoffreserv.UpdateResoffre(res1);
        //resoffreserv.DeleteResoffre(res1);
        /*
        List<Resoffre> MyResoffreList = new ArrayList<>( resoffreserv.ViewResoffre() ) ; 
        for (Resoffre resoff : MyResoffreList ) {
            System.out.println("user id  : " + resoff.getId_user() + " , " + " offre id  : " 
            + resoff.getId_offre() + " , "+ " nbr_de place  : " + resoff.getNbr_place() +
            " , " + "date reservation : " + resoff.getDate_res() ) ; 
            
        }
        */
                                            // vol hotel guide
        Offre off = new Offre("hhhhhhhhh", "Germany", 7, 11, 10, 500, 10,
        firstDate1, firstDate2, "hhhhhhwwww", "hasdh");
        ServicesOffre serviceoff = new ServicesOffre() ; 

        System.out.println(serviceoff.GetListVol()) ;
       // Offre x =  serviceoff.Search(27);
        
        //off.setId(28);
        //serviceoff.UpdateOffre(off);
        /*
        List<Offre> MyoffreList = new ArrayList<>( serviceoff.SortOffre(0) ) ; 
        for (Offre off1 : MyoffreList ) {
            System.out.println("offre name  : " + off1.getName() + " , " + off1.getPlace_dispo() ) ; 
            
        }*/
        
        // serviceoff.CreateOffre(off); 

        try {

            String file_name = "C:\\Users\\hbaie\\Desktop\\test.pdf" ; 
            Document document = new Document() ;
            PdfWriter.getInstance(document ,  new FileOutputStream(file_name) ) ;
            document.open() ;  

            
            List<Offre> MyoffreList = new ArrayList<>( serviceoff.Viewoffre() ) ; 
            for (Offre off1 : MyoffreList ) {
                Paragraph p = new Paragraph(off1.getName() + " " + off1.getDestination() 
                + " " + off1.getValablede() + " " + off1.getValablea() + 
                " " + off1.getPrice() + " " + off1.getPlace_dispo() + 
                " " + off1.getImage() + " " + off1.getDescription() + " " + off1.getVol() + 
                " " + off1.getHotel() + " " + off1.getGuide() ) ;  
                document.add(p) ;
                document.add(new Paragraph(" ")) ; 
                
            }

             
            document.close() ;

        } catch (Exception e) {
            //TODO: handle exception
        }




       // resoffreserv.trendingoffre() ; 

        //serviceoff.stat() ; 

        /*
        $image = $form->get('image')->getData();
                $file = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                $offre->setImage($file);
        */
    
    
    
    }



}