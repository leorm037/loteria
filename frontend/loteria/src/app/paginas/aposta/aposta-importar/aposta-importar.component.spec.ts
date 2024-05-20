import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ApostaImportarComponent } from './aposta-importar.component';

describe('ApostaImportarComponent', () => {
  let component: ApostaImportarComponent;
  let fixture: ComponentFixture<ApostaImportarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ApostaImportarComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ApostaImportarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
