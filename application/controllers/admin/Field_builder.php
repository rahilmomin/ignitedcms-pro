<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Field_builder extends CI_Controller {
   
    public function __construct()
	{
		
		parent::__construct();
		{
			if($this->session->userdata('isloggedin')=='1')
			{
				$this->load->model('Stuff_permissions');
				$pass = $this->Stuff_permissions->has_permission("field_builder");

				if($pass != true)
				{
					redirect('admin/dashboard','refresh');
				}
			}
			else
			{
				redirect('admin/installer','refresh');
			}
		}

		
	}




	public function index()
	{
		$this->db->select('*');
		$this->db->from('fields');
		$query = $this->db->get();
		
		$data['query'] = $query;

		$this->load->view('admin/header');
		$this->load->view('admin/body');
		$this->load->view('admin/fields/default',$data); 
		$this->load->view('admin/fields/footer');
		
		
		
	}

	 /**
	  *  @Description: dumps all fields related to section id
	  *       @Params: params
	  *
	  *  	 @returns: returns
	  */

	public function render_section($sectionid)
	{
		$this->db->select('*');
		$this->db->from('section');
		$this->db->where('sectionid', $sectionid);

		$query = $this->db->get();
		
		// foreach ($query->result() as $row) 
		// {
		// 	echo $row->fieldid;
		// }
		
		$data['query'] = $query;

		$this->load->view('admin/header');
		$this->load->view('admin/body');
		$this->load->view('admin/fields/render',$data); 
		$this->load->view('admin/fields/footer');



	}


	 /**
	  *  @Description: add a new field to db
	  *       @Params: none
	  *
	  *  	 @returns: returns
	  */
	public function add_new()
	{
		$data['title'] = 'Fields';
		
		// $this->db->select('*');
		// $this->db->from('fields');
		// $query = $this->db->get();
		
		

		//$data['query'] = $query;

		$this->load->view('admin/header');
		$this->load->view('admin/body');
		$this->load->view('admin/fields/detail',$data); 
		$this->load->view('admin/fields/footer');

	}

	/**
	  *  @Description: search the database for posts or delete
	  *       @Params: _post search_term
	  *
	  *  	 @returns: returns
	  */
	public function search_posts_or_delete()
	{
		//check if search or delete
		if($this->input->post('sbm') == "search") 
		{

			$search_term = $this->input->post('search_term');

			$this->db->select('*');
			$this->db->from('fields');
			$this->db->like('name', $search_term);

			$query = $this->db->get();
			

			$data['query'] = $query;
			


			$this->load->view('admin/header');
			$this->load->view('admin/body');
			$this->load->view('admin/fields/default',$data); 
			$this->load->view('admin/fields/footer');
		}

		if($this->input->post('sbm') == "delete") 
		{
			
			$this->load->model('Stuff_fields');

			//iterate over selected items and delete
			if (isset($_POST['chosen']))
			{
				$arrayName = $_POST['chosen'];

				foreach ($arrayName as $key => $value) {
					
					$this->Stuff_fields->remove_field($value);

					//delete the pages in the db
					$this->db->where('id', $value);
					$this->db->delete('fields');

				}
				
			}
			
			//return to page view
			redirect("admin/field_builder","refresh");
		
		}
	}



	 /**
	  *  @Description: load the field page set type
	  *       @Params: field id
	  *
	  *  	 @returns: nothing
	  */

	public function show_field_page($id)
	{
		

		$data['title'] = 'Fields';
		
		

		$this->load->view('admin/header');
		$this->load->view('admin/body');
		$this->load->view('admin/fields/detail',$data); 
		$this->load->view('admin/fields/footer');

	}

	 /**
	  *  @Description: validate and save the post field into db
	  *                save options as json
	  *       @Params: params
	  *
	  *  	 @returns: returns
	  */
	public function save_field($id)
	{

		$this->form_validation->set_rules('handle', 'Handle', 'required|alpha|is_unique[fields.name]'); //unique
		$this->form_validation->set_rules('type', 'Type', 'callback_type_check');
		$this->form_validation->set_rules('maxchars', 'Maxchar', 'integer|greater_than[0]');
		$this->form_validation->set_rules('limit', 'Limt', 'integer|greater_than[0]');



		$handle = $this->input->post('handle');
		$type = $this->input->post('type');
		$opts = "";
		$instructions = $this->input->post('instructions');
		$maxchars = $this->input->post('maxchars');
		$limitamount = $this->input->post('limit');
		$formvalidation = $this->input->post('filetypes');


		$min = $this->input->post('min');
		$max = $this->input->post('max');
		
		
		if ($this->form_validation->run() == FALSE)
		{
			 // $message =  validation_errors(); 
			 // $this->session->set_flashdata('type', '0');
			 // $this->session->set_flashdata('msg', "<strong>Failed</strong> $message");

			 $this->load->view('admin/header');
			 $this->load->view('admin/body');
			 $this->load->view('admin/fields/detail'); 
			 $this->load->view('admin/fields/footer');
		}
		else
		{

			foreach($_POST as $key => $value) 
    		{
    			if($this->startsWith($key, "opts"))
    			{
    				$opts = $opts . $value . ",";
    			}
    			
    		}

    		$opts =  trim($opts,",");
    		//pass this in as a comma delimited string!


			//successful
			$this->load->model('Stuff_fields');
			$this->Stuff_fields->add_new_field($handle,$type,$opts,$instructions,$maxchars,$limitamount,$formvalidation,$min,$max);




			$message = "Field Added!";
			$this->session->set_flashdata('type', '1');
			$this->session->set_flashdata('msg', "<strong>Success</strong> $message");

			$this->db->select('*');
			$this->db->from('fields');
			$query = $this->db->get();

			$data['query'] = $query;

			$this->load->view('admin/header');
			$this->load->view('admin/body');
			$this->load->view('admin/fields/default',$data); 
			$this->load->view('admin/fields/footer');
		}

	}

	 /**
	  *  @Description: custom function to check if _POST name starts with opts
	  *       @Params: string, startwith string
	  *
	  *  	 @returns: true or false
	  */
	public function startsWith($haystack, $needle)
	{
	     $length = strlen($needle);
	     return (substr($haystack, 0, $length) === $needle);
	}


	 /**
	  *  @Description: Custom call back function to see if a drop down has been chosen
	  *       @Params: params
	  *
	  *  	 @returns: returns
	  */

	public function type_check($str)
    {
            if ($str == 'Please select')
            {
                    $this->form_validation->set_message('type_check', 'Please choose a Field Type');
                    return FALSE;
            }
            else
            {
                    return TRUE;
            }
    }



}

/* End of file Field_builder.php */
/* Location: ./application/controllers/admin/Field_builder.php */