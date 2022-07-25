/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/javafx/FXMLController.java to edit this template
 */
package gui;

import java.net.URL;
import java.util.ResourceBundle;

import Services.ServicesGuide;
import edu.entities.Guide;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Button;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;

/**
 * FXML Controller class
 *
 * @author hbaie
 */
public class GuideViewController implements Initializable {

    @FXML
    private TableView<Guide> tableguide;
    @FXML
    private TableColumn<Guide, String> colname;
    @FXML
    private TableColumn<Guide, String> colemail;
    @FXML
    private TableColumn<Guide, Integer> colnbrexperience;
    @FXML
    private TableColumn<Guide, String> coldescription;

    @FXML
    private Button create;
    @FXML
    private Button update;
    @FXML
    private Button delete;
    @FXML
    private TextField tfname;
    @FXML
    private TextField tfemail;
    @FXML
    private TextField tfnbrexperience;
    @FXML
    private TextField tfdescription;
    @FXML
    private TextField tfid;

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        showGuide() ; 
        
        tableguide.setOnMouseClicked( e -> {
            //System.out.println(tableguide.getSelectionModel().getSelectedItem().getName())  ;  
            Guide g = new Guide();
            
            g = tableguide.getSelectionModel().getSelectedItem() ; 
            //System.out.println(g.getName()) ;
            tfid.setText( String.valueOf( g.getId())) ; 
            tfname.setText(g.getName()) ; 
            tfemail.setText(g.getEmail()) ;
            tfnbrexperience.setText( String.valueOf(g.getNbr_expr()) )  ; 
            tfdescription.setText(g.getDescription()) ; 


            });
        
        
    }  
    
    public void showGuide()
    {
        ServicesGuide guideserv = new ServicesGuide();
        ObservableList list = guideserv.ViewGuidefx() ; 

        colname.setCellValueFactory( new PropertyValueFactory<Guide,String>("name") );
        colemail.setCellValueFactory( new PropertyValueFactory<Guide,String>("email") );
        colnbrexperience.setCellValueFactory( new PropertyValueFactory<Guide,Integer>("nbr_expr") );
        coldescription.setCellValueFactory( new PropertyValueFactory<Guide,String>("description") );
        
        tableguide.setItems(list);
        

    }

    public void events(){
        System.out.println("test test   ") ;
    }


    @FXML
    private void create_guide(ActionEvent event) {

        Guide g = new Guide();
        g.setName(tfname.getText()); 
        g.setEmail(tfemail.getText()); 
        g.setNbr_expr( Integer.parseInt(tfnbrexperience.getText()) );
        g.setDescription(tfdescription.getText());
        ServicesGuide guideserv = new ServicesGuide();
        guideserv.CreateGuide2(g);
        showGuide() ; 
    }


    @FXML
    private void delete_guide(ActionEvent event) {
        Guide g = new Guide();
        g.setId(Integer.parseInt( tfid.getText())) ; 
        
        ServicesGuide guideserv = new ServicesGuide();
        guideserv.DeleteGuide(g);
        showGuide() ; 

    }

    @FXML
    private void update_guide(ActionEvent event) {
        Guide g = new Guide();
        g.setId(Integer.parseInt( tfid.getText())) ; 
        g.setName(tfname.getText()); 
        g.setEmail(tfemail.getText()); 
        g.setNbr_expr( Integer.parseInt(tfnbrexperience.getText()) );
        g.setDescription(tfdescription.getText());
        ServicesGuide guideserv = new ServicesGuide();
        guideserv.UpdateGuide(g);
        showGuide() ; 

        tfid.setText("") ;
        tfname.setText("") ;
        tfdescription.setText("") ;
        tfemail.setText("") ;
        tfnbrexperience.setText("") ; 



    }



    
}
