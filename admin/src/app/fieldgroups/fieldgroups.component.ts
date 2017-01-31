import { Component, OnInit } from '@angular/core';
import { ApiEnService } from '../service/api.en.service';
import { Fieldgroup } from '../data/fieldgroup';
import { ToasterService } from 'angular2-toaster';

@Component({
  selector: 'app-fieldgroups',
  templateUrl: './fieldgroups.component.html',
  styleUrls: ['./fieldgroups.component.css']
})
export class FieldgroupsComponent implements OnInit {

  entity_url: string = 'fieldgroups/all';
  fieldgroups: Fieldgroup[];
  errorMessage: string;

  constructor(private apiEnService: ApiEnService, private toasterService: ToasterService) { }

  ngOnInit() {
    this.apiEnService.getAll(this.entity_url)
      .subscribe(
      fieldgroups => this.fieldgroups = <Fieldgroup[]>fieldgroups,
      error => { this.errorMessage = <any>error; this.toasterService.pop('error', 'Error', 'Error with loading fieldgroups'); }
      );
  }
}