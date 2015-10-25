<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stuff_section extends CI_Model {

	 

	 /**
	  *  @Description: updates the sections fields
	  *       @Params: sectionid,handle, sectiontype, fields array
	  *
	  *  	 @returns: returns
	  */

	public function update_section($sectionid,$handle,$sectiontype,$fields)
	{
		//first delete previous content
		$this->db->where('sectionid', $sectionid);
		$this->db->delete('section_layout');

		$arrayName = explode(",", $fields);

		foreach ($arrayName as $value) {

			$tmp = explode(":",$value);
			$object2 = array('sortorder' => $tmp[0], 'fieldid' => $tmp[1], 'sectionid' =>$sectionid ,'required' => $tmp[2]);

			$this->db->insert('section_layout', $object2);
		}

	}



	 /**
	  *  @Description: save section
	  *       @Params: handle,sectiontype,fields as array
	  *
	  *  	 @returns: returns
	  */
	public function save_section($handle,$sectiontype,$fields)
	{

		$object1 = array('name' => $handle , 'sectiontype' => $sectiontype );
		$this->db->insert('section', $object1);

		$sectionid = $this->db->insert_id();


		$arrayName = explode(",", $fields);

		foreach ($arrayName as $value) {

			$tmp = explode(":",$value);
			$object2 = array('sortorder' => $tmp[0], 'fieldid' => $tmp[1], 'sectionid' =>$sectionid , 'required' => $tmp[2] );

			$this->db->insert('section_layout', $object2);
		}

		//insert into entry!!!
		//todo insert date created or modified

		

		
		//if sectiontype is equal add single ONLY!!!!!
		if ($sectiontype == "Single")
		{
			$object3 = array('sectionid' => $sectionid , 'type' => $sectiontype );
			$this->db->insert('entry', $object3);

			$entryid = $this->db->insert_id();

			$object4 = array('entryid' => $entryid );
			$this->db->insert('content', $object4);


			//insert into content!!

		}

		//if sectiontype is equal to global
		if ($sectiontype == "Global")
		{
			$object3 = array('sectionid' => $sectionid , 'type' => $sectiontype );
			$this->db->insert('entry', $object3);

			$entryid = $this->db->insert_id();

			$object4 = array('entryid' => $entryid );
			$this->db->insert('content', $object4);


			//insert into content!!

		}

	}

}

/* End of file Stuff_section.php */
/* Location: ./application/models/Stuff_section.php */