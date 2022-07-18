/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/javafx/FXMLController.java to edit this template
 */
package gui;

import java.io.File;
import java.net.URL;
import java.sql.Date;
import java.time.LocalDate;
import java.util.ResourceBundle;

import javax.swing.JFileChooser;

import Services.ServicesOffre;
import edu.entities.Offre;
import javafx.beans.property.SimpleStringProperty;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.DatePicker;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;

/**
 * FXML Controller class
 *
 * @author hbaie
 */
public class OffreViewController implements Initializable {

    @FXML
    private Button btnimage;
    @FXML
    private TableView<Offre> tableoffre;
    @FXML
    private TableColumn<Offre, String> colname;
    @FXML
    private TableColumn<Offre, String> coldestination;
    @FXML
    private TableColumn<Offre, Integer> colplace_dispo;
    @FXML
    private TableColumn<Offre, Integer> colprix;
    @FXML
    private TableColumn<Offre, Date> colvalable_de;
    @FXML
    private TableColumn<Offre, Date> colvalable_a;
    @FXML
    private TableColumn<Offre, String> colimage;
    @FXML
    private TableColumn<Offre, String> coldescription;
    @FXML
    private TableColumn<Offre, String> colguide;
    @FXML
    private TableColumn<Offre, String> colhotel;
    @FXML
    private TableColumn<Offre, String> colvol;
    @FXML
    private TextField tfid;
    @FXML
    private TextField tfname;
    @FXML
    private TextField tfdestination;
    @FXML
    private TextField tfplacedispo;
    @FXML
    private TextField tfprix;
    @FXML
    private DatePicker tfvalablede;
    @FXML
    private DatePicker tfvalablea;
    @FXML
    private TextField tfimage;
    @FXML
    private TextField tfdescription;
    @FXML
    private ComboBox<String> combovol;
    @FXML
    private ComboBox<String> combohotel;
    @FXML
    private ComboBox<String> comboguide;
    @FXML
    private Button btnadd;
    @FXML
    private Button btnupdate;
    @FXML
    private Button btndelete;
    @FXML
    private Button btnsort;

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        ServicesOffre offreserv = new ServicesOffre();

        combovol.setItems(offreserv.GetListVol()) ; 
        combohotel.setItems(offreserv.GetListHotel()) ; 
        comboguide.setItems(offreserv.GetListGuide()) ; 

        showOffre() ;

        tableoffre.setOnMouseClicked( e -> {
            //System.out.println(tableguide.getSelectionModel().getSelectedItem().getName())  ;  
            Offre off = new Offre();
            off = tableoffre.getSelectionModel().getSelectedItem() ; 
            //System.out.println(g.getName()) ;
            tfid.setText(String.valueOf(off.getId()) ) ; 
            tfimage.setText(off.getImage());
            tfplacedispo.setText(String.valueOf(off.getPlace_dispo()) ) ; 
            tfprix.setText(String.valueOf(off.getPrice()) ) ; 
            tfvalablede.setValue(off.getValablede().toLocalDate() ) ; 
            tfvalablea.setValue(off.getValablea().toLocalDate() ) ;

            tfname.setText(off.getName()) ;
            tfdestination.setText(off.getDestination()) ; 
            tfdescription.setText(off.getDescription()) ; 

           // System.out.println(off.toString()) ;


            });
        
        
    }    

    @FXML
    private void uploadimage(ActionEvent event) {
        JFileChooser chooser = new JFileChooser();
        chooser.showOpenDialog(null) ; 
        File f =chooser.getSelectedFile();
        String filename = f.getAbsolutePath() ; 
      //  System.out.println(filename) ;
        
      //  System.out.println(filename.indexOf( "\\", 32));
        System.out.println(filename.substring(39) );

        tfimage.setText(filename.substring(39));


    }


    public void showOffre()
    {
        ServicesOffre offreserv = new ServicesOffre();
        ObservableList list = offreserv.ViewOffrefx() ; 
        colname.setCellValueFactory( new PropertyValueFactory<Offre,String>("name") );
        coldestination.setCellValueFactory( new PropertyValueFactory<Offre,String>("destination") );
        
        colplace_dispo.setCellValueFactory( new PropertyValueFactory<Offre,Integer>("place_dispo") );
        colprix.setCellValueFactory( new PropertyValueFactory<Offre,Integer>("price") );
        colvalable_de.setCellValueFactory( new PropertyValueFactory<Offre,Date>("valablede") );
        colvalable_a.setCellValueFactory( new PropertyValueFactory<Offre,Date>("valablea") );
        colimage.setCellValueFactory( new PropertyValueFactory<Offre,String>("image") );
        coldescription.setCellValueFactory( new PropertyValueFactory<Offre,String>("description") );
        colguide.setCellValueFactory( cellData -> new SimpleStringProperty(offreserv.GetGuideName(cellData.getValue().getGuide()) ) );
        colvol.setCellValueFactory( cellData -> new SimpleStringProperty(offreserv.GetVolName(cellData.getValue().getVol()) ) );
        colhotel.setCellValueFactory( cellData -> new SimpleStringProperty(offreserv.GetHotelName(cellData.getValue().getHotel()) ) );

        tableoffre.setItems(list);
        

    }

    @FXML
    private void addoffre(ActionEvent event) {
        ServicesOffre offreserv = new ServicesOffre();
        Offre off = new Offre();
        off.setName(tfname.getText() ) ; 
        off.setDestination(tfdestination.getText() );
        off.setPlace_dispo(Integer.parseInt( tfplacedispo.getText()) ) ;
        off.setPrice(Integer.parseInt( tfprix.getText() ) ) ;
        off.setValablede(Date.valueOf(tfvalablede.getValue())) ;
        off.setValablea(Date.valueOf(tfvalablea.getValue())) ;
        off.setImage(tfimage.getText() )  ; 
        off.setDescription( tfdescription.getText() ); 
        off.setGuide(offreserv.GetGuideId( comboguide.getSelectionModel().getSelectedItem().toString() ) );
        off.setHotel(offreserv.GetHotelId( combohotel.getSelectionModel().getSelectedItem().toString() ) );
        off.setVol(offreserv.GetVolId( combovol.getSelectionModel().getSelectedItem().toString() ) );
    
        offreserv.CreateOffre(off);
        showOffre() ; 


    }

    @FXML
    private void updateoffre(ActionEvent event) {
        ServicesOffre offreserv = new ServicesOffre();
        Offre off = new Offre();
        off.setId(Integer.parseInt(tfid.getText())) ; 
        off.setName(tfname.getText() ) ; 
        off.setDestination(tfdestination.getText() );
        off.setPlace_dispo(Integer.parseInt( tfplacedispo.getText()) ) ;
        off.setPrice(Integer.parseInt( tfprix.getText() ) ) ;
        off.setValablede(Date.valueOf(tfvalablede.getValue())) ;
        off.setValablea(Date.valueOf(tfvalablea.getValue())) ;
        off.setImage(tfimage.getText() )  ; 
        off.setDescription( tfdescription.getText() ); 
        off.setGuide(offreserv.GetGuideId( comboguide.getSelectionModel().getSelectedItem().toString() ) );
        off.setHotel(offreserv.GetHotelId( combohotel.getSelectionModel().getSelectedItem().toString() ) );
        off.setVol(offreserv.GetVolId( combovol.getSelectionModel().getSelectedItem().toString() ) );
        offreserv.UpdateOffre(off);
        showOffre() ;

        tfid.setText("");
        tfname.setText("");
        tfdestination.setText("");
        tfdescription.setText("");
        tfplacedispo.setText("");
        tfprix.setText("");
        tfimage.setText("") ;
        tfvalablea.setValue(null); 
        tfvalablede.setValue(null);
        combovol.getSelectionModel().clearSelection(); 
        combohotel.getSelectionModel().clearSelection(); 
        comboguide.getSelectionModel().clearSelection(); 
    }

    @FXML
    private void deleteoffre(ActionEvent event) {

        Offre off = new Offre();
        off.setId(Integer.parseInt( tfid.getText())) ; 
        
        ServicesOffre offreserv = new ServicesOffre();
        offreserv.DeleteOffre(off);
        showOffre() ; 
    }

    @FXML
    private void sortoffre(ActionEvent event) {
        ServicesOffre offreserv = new ServicesOffre();
        ObservableList list = offreserv.SortOffrefx() ; 
        colname.setCellValueFactory( new PropertyValueFactory<Offre,String>("name") );
        coldestination.setCellValueFactory( new PropertyValueFactory<Offre,String>("destination") );
        
        colplace_dispo.setCellValueFactory( new PropertyValueFactory<Offre,Integer>("place_dispo") );
        colprix.setCellValueFactory( new PropertyValueFactory<Offre,Integer>("price") );
        colvalable_de.setCellValueFactory( new PropertyValueFactory<Offre,Date>("valablede") );
        colvalable_a.setCellValueFactory( new PropertyValueFactory<Offre,Date>("valablea") );
        colimage.setCellValueFactory( new PropertyValueFactory<Offre,String>("image") );
        coldescription.setCellValueFactory( new PropertyValueFactory<Offre,String>("description") );
        colguide.setCellValueFactory( cellData -> new SimpleStringProperty(offreserv.GetGuideName(cellData.getValue().getGuide()) ) );
        colvol.setCellValueFactory( cellData -> new SimpleStringProperty(offreserv.GetVolName(cellData.getValue().getVol()) ) );
        colhotel.setCellValueFactory( cellData -> new SimpleStringProperty(offreserv.GetHotelName(cellData.getValue().getHotel()) ) );
        tableoffre.setItems(list);
        
    }

    
    



    
}